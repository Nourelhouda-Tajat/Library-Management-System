<?php
class BorrowRecord {
    private int $recordId;
    private Member $member;
    private Book $book;
    private LibraryBranch $branch;
    private DateTime $borrowDate;
    private DateTime $dueDate;
    private ?DateTime $returnDate;
    private float $lateFee;
    private bool $renewed;
    
    public function __construct($recordId, $member, $book, $branch) {
        $this->recordId = $recordId;
        $this->member = $member;
        $this->book = $book;
        $this->branch = $branch;
        $this->borrowDate = new DateTime();
        
        // Calcul simple sans clone
        $days = $member->getLoanPeriod();
        $this->dueDate = new DateTime();
        $this->dueDate->modify("+$days days");
        
        $this->returnDate = null;
        $this->lateFee = 0;
        $this->renewed = false;
        
        // Mettre à jour le statut du livre
        $book->setStatus('CHECKED_OUT');
        $member->incrementBorrowed();
    }
    
    public function isOverdue(): bool {
        if ($this->returnDate !== null) {
            return false;
        }
        return new DateTime() > $this->dueDate;
    }
    
    public function calculateLateFee(): float {
        if ($this->isOverdue()) {
            $today = new DateTime();
            if ($today > $this->dueDate) {
                $diff = $today->diff($this->dueDate);
                $daysOverdue = $diff->days;
                $this->lateFee = $daysOverdue * $this->member->getLateFeeRate();
            }
        }
        return $this->lateFee;
    }
    
    public function returnBook(): void {
        $this->returnDate = new DateTime();
        $this->calculateLateFee();
        
        if ($this->lateFee > 0) {
            $this->member->addFee($this->lateFee);
        }
        
        $this->book->setStatus('AVAILABLE');
        $this->member->decrementBorrowed();
    }
    
    public function canRenew(): bool {
        return !$this->renewed && 
               !$this->isOverdue() && 
               $this->book->getStatus() !== 'RESERVED';
    }
    
    public function renew(): bool {
        if ($this->canRenew()) {
            $days = $this->member->getLoanPeriod();
            $this->dueDate->modify("+$days days");
            $this->renewed = true;
            return true;
        }
        return false;
    }
    
    public function getDueDate(): DateTime {
        return $this->dueDate;
    }
}
?>