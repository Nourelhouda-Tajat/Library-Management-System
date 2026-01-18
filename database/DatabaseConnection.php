<?php
class DatabaseConnection {
    private static ?PDO $connection = null;
    
    private function __construct() {}
    
    public static function getConnection(): PDO {
        if (self::$connection === null) {
            try {
                $host = 'localhost';
                $dbname = 'librarysystem';
                $username = 'root';
                $password = '';
                
                self::$connection = new PDO(
                    "mysql:host=$host;dbname=$dbname;charset=utf8",
                    $username,
                    $password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]
                );
            } catch (PDOException $e) {
                die("Erreur de connexion: " . $e->getMessage());
            }
        }
        
        return self::$connection;
    }
}
?>