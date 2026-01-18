<?php
class BorrowRecordRepository {
    private PDO $db;
    
    public function __construct() {
        $this->db = DatabaseConnection::getConnection();
    }
    
    public function create(array $recordData): bool {
        $stmt = $this->db->prepare("
            INSERT INTO BorrowRecord (member_id, book_isbn, branch_id, borrow_date, due_date, return_date, late_fee, renewed)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $recordData['member_id'],
            $recordData['book_isbn'],
            $recordData['branch_id'],
            $recordData['borrow_date'],
            $recordData['due_date'],
            $recordData['return_date'] ?? null,
            $recordData['late_fee'] ?? 0,
            $recordData['renewed'] ?? 0
        ]);
    }
    
    public function updateReturn(int $recordId, string $returnDate, float $lateFee): bool {
        $stmt = $this->db->prepare("
            UPDATE BorrowRecord 
            SET return_date = ?, late_fee = ? 
            WHERE record_id = ?
        ");
        return $stmt->execute([$returnDate, $lateFee, $recordId]);
    }
    
    public function updateRenewal(int $recordId, string $newDueDate): bool {
        $stmt = $this->db->prepare("
            UPDATE BorrowRecord 
            SET due_date = ?, renewed = 1 
            WHERE record_id = ?
        ");
        return $stmt->execute([$newDueDate, $recordId]);
    }
    
    public function findActiveByMember(int $memberId): array {
        $stmt = $this->db->prepare("
            SELECT * FROM BorrowRecord 
            WHERE member_id = ? AND return_date IS NULL
        ");
        $stmt->execute([$memberId]);
        return $stmt->fetchAll();
    }
    
    public function findActiveByBook(string $isbn): array {
        $stmt = $this->db->prepare("
            SELECT * FROM BorrowRecord 
            WHERE book_isbn = ? AND return_date IS NULL
        ");
        $stmt->execute([$isbn]);
        return $stmt->fetchAll();
    }
    
    public function findOverdue(): array {
        $today = date('Y-m-d');
        $stmt = $this->db->prepare("
            SELECT * FROM BorrowRecord 
            WHERE return_date IS NULL AND due_date < ?
        ");
        $stmt->execute([$today]);
        return $stmt->fetchAll();
    }
}
?>