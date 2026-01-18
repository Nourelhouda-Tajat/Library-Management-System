<?php
class Author {
    private int $authorId;
    private string $name;
    private string $bio;
    private string $nationality;
    private ?DateTime $birthDate;
    private ?DateTime $deathDate;
    private string $primaryGenre;
    
    public function __construct($authorId, $name) {
        $this->authorId = $authorId;
        $this->name = $name;
    }
    
    public function getAuthorId(): int {
        return $this->authorId;
    }
    
    public function getName(): string {
        return $this->name;
    }
    
    public function setBiography(string $bio): void {
        $this->bio = $bio;
    }
    
    public function getBiography(): string {
        return $this->bio;
    }
}
?>