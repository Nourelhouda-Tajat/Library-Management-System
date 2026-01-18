<?php
require_once __DIR__ . '/../database/DatabaseConnection.php';

require_once(__DIR__ . '/../src/Repositories/BookRepository.php');
require_once(__DIR__ . '/../src/Repositories/MemberRepository.php'); // NEW
require_once(__DIR__ . '/../src/Repositories/BorrowRecordRepository.php');
require_once(__DIR__ . '/../src/Repositories/InventoryRepository.php');
require_once(__DIR__ . '/../src/Repositories/ReservationRepository.php');
require_once(__DIR__ . '/../src/Services/LibraryService.php');

echo "=== TEST BASE DE DONNÉES ===\n\n";

try {
    $service = new LibraryService();
    
    $memberData = [
        'full_name' => 'Test Student',
        'email' => 'test.student@email.com',
        'phone' => '0612345678',
        'membership_expiry' => '2024-12-31',
        'member_type' => 'STUDENT',
        'is_graduate' => 0
    ];
    
    $memberRepo = new MemberRepository();
    if ($memberRepo->save($memberData)) {
        echo "✓ Membre créé avec succès\n";
        
        // Récupérer l'ID du membre
        $member = $memberRepo->findByEmail('test.student@email.com');
        
        if ($member) {
            echo "✓ Membre trouvé: {$member['full_name']}\n";
            
            // Tester l'emprunt (supposons que le livre et l'inventaire existent)
            echo "\nTest d'emprunt:\n";
            try {
                // Ces données doivent correspondre à des données existantes dans la BD
                $success = $service->borrowBook(
                    $member['member_id'],
                    '978-0747532699', // ISBN existant
                    1 // branch_id existant
                );
                
                if ($success) {
                    echo "✓ Emprunt réussi\n";
                    
                    // Tester la réservation
                    echo "\nTest de réservation:\n";
                    $success = $service->reserveBook(
                        2, // Un autre membre
                        '978-0747532699',
                        1
                    );
                    
                    if ($success) {
                        echo "✓ Réservation créée\n";
                    }
                }
            } catch (Exception $e) {
                echo "✗ Erreur: " . $e->getMessage() . "\n";
            }
            
            // Tester les rapports
            echo "\n=== RAPPORTS ===\n";
            $overdue = $service->getOverdueBooks();
            echo "Livres en retard: " . count($overdue) . "\n";
            
            $expired = $service->getExpiredReservations();
            echo "Réservations expirées: " . count($expired) . "\n";
        }
    } else {
        echo "✗ Erreur lors de la création du membre\n";
    }
} catch (Exception $e) {
    echo "Erreur générale: " . $e->getMessage() . "\n";
}

echo "\n=== FIN DU TEST ===\n";
?>