<?php
class LibrarySystem {
    private array $borrowRecords = [];
    private array $inventories = [];
    
    public function borrowBook(Member $member, Book $book, LibraryBranch $branch): ?BorrowRecord {
        if (!$member->canBorrow()) {
            return null;
        }
        
        if (!$book->isAvailable()) {
            return null;
        }
        
        // Vérifier l'inventaire
        $inventory = $this->getInventory($book);
        if ($inventory === null || $inventory->getAvailableCopies() === 0) {
            return null;
        }
        
        // Créer l'enregistrement
        $recordId = count($this->borrowRecords) + 1;
        $borrowRecord = new BorrowRecord($recordId, $member, $book, $branch);
        
        // Mettre à jour l'inventaire
        $inventory->decreaseAvailable();
        
        $this->borrowRecords[] = $borrowRecord;
        return $borrowRecord;
    }
    
    public function returnBook(BorrowRecord $borrowRecord): void {
        $borrowRecord->returnBook();
        
        $inventory = $this->getInventory($borrowRecord->getBook());
        if ($inventory !== null) {
            $inventory->increaseAvailable();
        }
    }
    
    public function addInventory(Inventory $inventory): void {
        $this->inventories[] = $inventory;
    }
    
    private function getInventory(Book $book): ?Inventory {
        foreach ($this->inventories as $inventory) {
            if ($inventory->getBook() === $book) {
                return $inventory;
            }
        }
        return null;
    }
}
?>