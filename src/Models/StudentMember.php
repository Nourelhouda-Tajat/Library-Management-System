<?php
class StudentMember extends Member {
    private bool $isGraduate;
    
    public function __construct( $memberId, $fullName, $email, $membershipExpiry, $isGraduate) {
        parent::__construct($memberId, $fullName, $email, $membershipExpiry);
        $this->isGraduate = $isGraduate;
    }
    
    public function getBorrowLimit(): int {
        return 3;
    }
    
    public function getLoanPeriod(): int {
        return 14;
    }
    
    public function getLateFeeRate(): float {
        return 0.50;
    }
}
?>