<?php
class Reservation {
    private int $reservationId;
    private Member $member;
    private Book $book;
    private LibraryBranch $branch;
    private DateTime $reservationDate;
    private string $status;
    private DateTime $expiryDate;
    
    public function __construct($reservationId, $member, $book, $branch) {
        $this->reservationId = $reservationId;
        $this->member = $member;
        $this->book = $book;
        $this->branch = $branch;
        $this->reservationDate = new DateTime();
        $this->status = 'PENDING';
        
        // 48 heures = 2 jours
        $this->expiryDate = new DateTime();
        $this->expiryDate->modify('+2 days');
        
        if ($book->getStatus() === 'CHECKED_OUT') {
            $book->setStatus('RESERVED');
        }
    }
    
    public function cancel(): void {
        $this->status = 'CANCELLED';
        if ($this->book->getStatus() === 'RESERVED') {
            $this->book->setStatus('CHECKED_OUT');
        }
    }
    
    public function isExpired(): bool {
        return new DateTime() > $this->expiryDate;
    }
}
?>