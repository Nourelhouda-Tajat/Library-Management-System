<?php
class Book {
    private string $isbn;
    private string $title;
    private int $pubYear;
    private string $category;
    private string $status;
    
    public function __construct($isbn, $title, $pubYear, $category) {
        $this->isbn = $isbn;
        $this->title = $title;
        $this->pubYear = $pubYear;
        $this->category = $category;
        $this->status = 'AVAILABLE';
    }
    
    public function getIsbn(): string {
        return $this->isbn;
    }
    
    public function getTitle(): string {
        return $this->title;
    }
    
    public function getStatus(): string {
        return $this->status;
    }
    
    public function setStatus(string $status): void {
        $this->status = $status;
    }
    
    public function isAvailable(): bool {
        return $this->status === 'AVAILABLE';
    }
    
    public function canBeReserved(): bool {
        return $this->status === 'CHECKED_OUT';
    }
}
?>