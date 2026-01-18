<?php
class LibraryService {
    private BookRepository $bookRepo;
    private MemberRepository $memberRepo;
    private BorrowRecordRepository $borrowRepo;
    private ReservationRepository $reservationRepo;
    private InventoryRepository $inventoryRepo;
    
    public function __construct() {
        $this->bookRepo = new BookRepository();
        $this->memberRepo = new MemberRepository();
        $this->borrowRepo = new BorrowRecordRepository();
        $this->reservationRepo = new ReservationRepository();
        $this->inventoryRepo = new InventoryRepository();
    }
    
    public function borrowBook(int $memberId, string $isbn, int $branchId): bool {
        $member = $this->memberRepo->findById($memberId);
        if (!$member) {
            throw new Exception("Membre non trouvé");
        }
        
        $book = $this->bookRepo->findById($isbn);
        if (!$book) {
            throw new Exception("Livre non trouvé");
        }
        
        if ($book['status'] !== 'AVAILABLE') {
            throw new Exception("Livre non disponible");
        }
        
        $available = $this->inventoryRepo->getAvailableCopies($isbn, $branchId);
        if ($available <= 0) {
            throw new Exception("Plus de copies disponibles");
        }
        
        if ($member['unpaid_fees'] > 10.0) {
            throw new Exception("Frais impayés > 10€");
        }
        
        if ($member['total_borrowed'] >= $this->getBorrowLimit($member['member_type'])) {
            throw new Exception("Limite d'emprunt atteinte");
        }
        
        $loanPeriod = $this->getLoanPeriod($member['member_type']);
        $borrowDate = date('Y-m-d');
        $dueDate = date('Y-m-d', strtotime("+$loanPeriod days"));
        
        try {
            $this->borrowRepo->create([
                'member_id' => $memberId,
                'book_isbn' => $isbn,
                'branch_id' => $branchId,
                'borrow_date' => $borrowDate,
                'due_date' => $dueDate,
                'renewed' => 0
            ]);
            
            $this->bookRepo->updateStatus($isbn, 'CHECKED_OUT');
            
            $this->inventoryRepo->updateAvailableCopies($isbn, $branchId, -1);
            
            $this->memberRepo->updateBorrowedCount($memberId, 1);
            
            return true;
        } catch (Exception $e) {
            throw new Exception("Erreur lors de l'emprunt: " . $e->getMessage());
        }
    }
    
    public function returnBook(int $recordId): bool {
        $record = $this->borrowRepo->findById($recordId);
        if (!$record) {
            throw new Exception("Enregistrement non trouvé");
        }
        
        $today = date('Y-m-d');
        $lateFee = 0;
        
        if ($record['due_date'] < $today) {
            $daysLate = (strtotime($today) - strtotime($record['due_date'])) / (60 * 60 * 24);
            $member = $this->memberRepo->findById($record['member_id']);
            $feeRate = $this->getLateFeeRate($member['member_type']);
            $lateFee = $daysLate * $feeRate;
        }
        
        try {
            $this->borrowRepo->updateReturn($recordId, $today, $lateFee);
            
            $this->bookRepo->updateStatus($record['book_isbn'], 'AVAILABLE');
            
            $this->inventoryRepo->updateAvailableCopies($record['book_isbn'], $record['branch_id'], 1);
            
            if ($lateFee > 0) {
                $this->memberRepo->updateFees($record['member_id'], $lateFee);
            }
            
            $this->memberRepo->updateBorrowedCount($record['member_id'], -1);
            
            return true;
        } catch (Exception $e) {
            throw new Exception("Erreur lors du retour: " . $e->getMessage());
        }
    }
    
    public function reserveBook(int $memberId, string $isbn, int $branchId): bool {
        $book = $this->bookRepo->findById($isbn);
        if (!$book) {
            throw new Exception("Livre non trouvé");
        }
        
        if ($book['status'] !== 'CHECKED_OUT') {
            throw new Exception("Le livre doit être emprunté pour être réservé");
        }
        
        $existingReservations = $this->reservationRepo->findActiveByBook($isbn);
        if (count($existingReservations) > 0) {
            throw new Exception("Le livre a déjà une réservation en attente");
        }
        
        $reservationDate = date('Y-m-d H:i:s');
        $expiryDate = date('Y-m-d H:i:s', strtotime('+48 hours'));
        
        try {
            $this->reservationRepo->create([
                'member_id' => $memberId,
                'book_isbn' => $isbn,
                'branch_id' => $branchId,
                'reservation_date' => $reservationDate,
                'status' => 'PENDING',
                'expiry_date' => $expiryDate
            ]);
            
            $this->bookRepo->updateStatus($isbn, 'RESERVED');
            
            return true;
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la réservation: " . $e->getMessage());
        }
    }
    
    private function getBorrowLimit(string $memberType): int {
        return $memberType === 'STUDENT' ? 3 : 10;
    }
    
    private function getLoanPeriod(string $memberType): int {
        return $memberType === 'STUDENT' ? 14 : 30;
    }
    
    private function getLateFeeRate(string $memberType): float {
        return $memberType === 'STUDENT' ? 0.50 : 0.25;
    }
    
    public function getOverdueBooks(): array {
        return $this->borrowRepo->findOverdue();
    }
    
    public function getExpiredReservations(): array {
        return $this->reservationRepo->findExpired();
    }
}
?>