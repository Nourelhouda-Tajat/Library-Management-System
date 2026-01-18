<?php
class ReservationRepository {
    private PDO $db;
    
    public function __construct() {
        $this->db = DatabaseConnection::getConnection();
    }
    
    public function create(array $reservationData): bool {
        $stmt = $this->db->prepare("
            INSERT INTO Reservation (member_id, book_isbn, branch_id, reservation_date, status, expiry_date)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $reservationData['member_id'],
            $reservationData['book_isbn'],
            $reservationData['branch_id'],
            $reservationData['reservation_date'],
            $reservationData['status'],
            $reservationData['expiry_date']
        ]);
    }
    
    public function cancel(int $reservationId): bool {
        $stmt = $this->db->prepare("
            UPDATE Reservation 
            SET status = 'CANCELLED' 
            WHERE reservation_id = ?
        ");
        return $stmt->execute([$reservationId]);
    }
    
    public function findActiveByBook(string $isbn): array {
        $stmt = $this->db->prepare("
            SELECT * FROM Reservation 
            WHERE book_isbn = ? AND status = 'PENDING'
        ");
        $stmt->execute([$isbn]);
        return $stmt->fetchAll();
    }
    
    public function findExpired(): array {
        $now = date('Y-m-d H:i:s');
        $stmt = $this->db->prepare("
            SELECT * FROM Reservation 
            WHERE status = 'PENDING' AND expiry_date < ?
        ");
        $stmt->execute([$now]);
        return $stmt->fetchAll();
    }
    
    public function updateStatus(int $reservationId, string $status): bool {
        $stmt = $this->db->prepare("
            UPDATE Reservation 
            SET status = ? 
            WHERE reservation_id = ?
        ");
        return $stmt->execute([$status, $reservationId]);
    }
}
?>