<?php
// src/Repositories/MemberRepository.php
class MemberRepository {
    private PDO $db;
    
    public function __construct() {
        $this->db = DatabaseConnection::getConnection();
    }
    
    public function findById(int $memberId): ?array {
        $stmt = $this->db->prepare("SELECT * FROM Member WHERE member_id = ?");
        $stmt->execute([$memberId]);
        $member = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $member ?: null;
    }
    
    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare("SELECT * FROM Member WHERE email = ?");
        $stmt->execute([$email]);
        $member = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $member ?: null;
    }
    
    public function save(array $memberData): bool {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO Member (full_name, email, phone, membership_expiry, member_type, is_graduate, total_borrowed, unpaid_fees)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                full_name = VALUES(full_name),
                phone = VALUES(phone),
                membership_expiry = VALUES(membership_expiry),
                member_type = VALUES(member_type),
                is_graduate = VALUES(is_graduate),
                total_borrowed = VALUES(total_borrowed),
                unpaid_fees = VALUES(unpaid_fees)
            ");
            
            return $stmt->execute([
                $memberData['full_name'] ?? '',
                $memberData['email'] ?? '',
                $memberData['phone'] ?? '',
                $memberData['membership_expiry'] ?? date('Y-m-d', strtotime('+1 year')),
                $memberData['member_type'] ?? 'STUDENT',
                $memberData['is_graduate'] ?? 0,
                $memberData['total_borrowed'] ?? 0,
                $memberData['unpaid_fees'] ?? 0
            ]);
        } catch (PDOException $e) {
            error_log("MemberRepository save error: " . $e->getMessage());
            return false;
        }
    }
    
    public function updateBorrowedCount(int $memberId, int $change): bool {
        $stmt = $this->db->prepare("
            UPDATE Member 
            SET total_borrowed = total_borrowed + ? 
            WHERE member_id = ?
        ");
        return $stmt->execute([$change, $memberId]);
    }
    
    public function updateFees(int $memberId, float $fee): bool {
        $stmt = $this->db->prepare("
            UPDATE Member 
            SET unpaid_fees = unpaid_fees + ? 
            WHERE member_id = ?
        ");
        return $stmt->execute([$fee, $memberId]);
    }
    
    public function findAll(): array {
        $stmt = $this->db->query("SELECT * FROM Member");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>