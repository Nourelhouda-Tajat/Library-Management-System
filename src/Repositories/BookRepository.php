<?php
class BookRepository {
    private PDO $db;
    
    public function __construct() {
        $this->db = DatabaseConnection::getConnection();
    }
    
    public function findById(string $isbn): ?array {
        $stmt = $this->db->prepare("SELECT * FROM Book WHERE isbn = ?");
        $stmt->execute([$isbn]);
        $book = $stmt->fetch();
        
        return $book ?: null;
    }
    
    public function findAll(): array {
        $stmt = $this->db->query("SELECT * FROM Book");
        return $stmt->fetchAll();
    }
    
    public function findByStatus(string $status): array {
        $stmt = $this->db->prepare("SELECT * FROM Book WHERE status = ?");
        $stmt->execute([$status]);
        return $stmt->fetchAll();
    }
    
    public function save(Book $book): bool {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO Book (isbn, title, publication_year, category, status)
                VALUES (?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                title = VALUES(title),
                publication_year = VALUES(publication_year),
                category = VALUES(category),
                status = VALUES(status)
            ");
            
            return $stmt->execute([
                $book->getIsbn(),
                $book->getTitle(),
                $book->getPublicationYear(),
                $book->getCategory(),
                $book->getStatus()
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function updateStatus(string $isbn, string $status): bool {
        $stmt = $this->db->prepare("UPDATE Book SET status = ? WHERE isbn = ?");
        return $stmt->execute([$status, $isbn]);
    }
}
?>