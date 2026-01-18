<?php
class InventoryRepository {
    private PDO $db;
    
    public function __construct() {
        $this->db = DatabaseConnection::getConnection();
    }
    
    public function findByBookAndBranch(string $isbn, int $branchId): ?array {
        $stmt = $this->db->prepare("
            SELECT * FROM Inventory 
            WHERE book_isbn = ? AND branch_id = ?
        ");
        $stmt->execute([$isbn, $branchId]);
        return $stmt->fetch() ?: null;
    }
    
    public function updateAvailableCopies(string $isbn, int $branchId, int $change): bool {
        $stmt = $this->db->prepare("
            UPDATE Inventory 
            SET available_copies = available_copies + ?, last_updated = NOW()
            WHERE book_isbn = ? AND branch_id = ?
        ");
        return $stmt->execute([$change, $isbn, $branchId]);
    }
    
    public function getAvailableCopies(string $isbn, int $branchId): int {
        $inventory = $this->findByBookAndBranch($isbn, $branchId);
        return $inventory ? $inventory['available_copies'] : 0;
    }
}
?>