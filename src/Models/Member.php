<?php
abstract class Member {
    private int $memberId;
    private string $fullName;
    private string $email;
    private DateTime $membershipExpiry;
    private int $borrowedCount;
    private float $unpaidFees;
    
    public function __construct($memberId, $fullName, $email, $membershipExpiry) {
        $this->memberId = $memberId;
        $this->fullName = $fullName;
        $this->email = $email;
        $this->membershipExpiry = $membershipExpiry;
        $this->borrowedCount = 0;
        $this->unpaidFees = 0;
    }
    
    public function getMemberId(): int {
        return $this->memberId;
    }
    
    public function getFullName(): string {
        return $this->fullName;
    }
    
    public function getBorrowedCount(): int {
        return $this->borrowedCount;
    }
    
    public function getUnpaidFees(): float {
        return $this->unpaidFees;
    }
    
    public function canBorrow(): bool {
        $today = new DateTime();
        return $this->membershipExpiry > $today && 
               $this->unpaidFees <= 10.0 && 
               $this->borrowedCount < $this->getBorrowLimit();
    }
    
    public function incrementBorrowed(): void {
        $this->borrowedCount++;
    }
    
    public function decrementBorrowed(): void {
        if ($this->borrowedCount > 0) {
            $this->borrowedCount--;
        }
    }
    
    public function addFee(float $amount): void {
        $this->unpaidFees += $amount;
    }
    
    public function getMembershipExpiry(): DateTime {
        return $this->membershipExpiry;
    }
    
    abstract public function getBorrowLimit(): int;
    abstract public function getLoanPeriod(): int;
    abstract public function getLateFeeRate(): float;
}
?>