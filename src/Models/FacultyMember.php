<?php
class FacultyMember extends Member {
    private string $role;
    
    public function __construct( $memberId, $fullName, $email, $membershipExpiry, $role) {
        parent::__construct($memberId, $fullName, $email, $membershipExpiry);
        $this->role = $role;
    }
    
    public function getBorrowLimit(): int {
        return 10;
    }
    
    public function getLoanPeriod(): int {
        return 30;
    }
    
    public function getLateFeeRate(): float {
        return 0.25;
    }
}
?>