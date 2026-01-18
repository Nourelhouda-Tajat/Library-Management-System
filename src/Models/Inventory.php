<?php
class Inventory {
    private int $inventoryId;
    private Book $book;
    private LibraryBranch $branch;
    private int $totalCopies;
    private int $availableCopies;
    
    public function __construct( $inventoryId, $book, $branch, $totalCopies) {
        $this->inventoryId = $inventoryId;
        $this->book = $book;
        $this->branch = $branch;
        $this->totalCopies = $totalCopies;
        $this->availableCopies = $totalCopies;
    }
    
    public function getBook(): Book {
        return $this->book;
    }
    
    public function getAvailableCopies(): int {
        return $this->availableCopies;
    }
    
    public function decreaseAvailable(): bool {
        if ($this->availableCopies > 0) {
            $this->availableCopies--;
            return true;
        }
        return false;
    }
    
    public function increaseAvailable(): bool {
        if ($this->availableCopies < $this->totalCopies) {
            $this->availableCopies++;
            return true;
        }
        return false;
    }
}
?>