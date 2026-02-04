<?php
namespace App\Controllers;

class MessagesController
{
    public function index(): void
    {
        \require_login();
        $pdo = \app_pdo();
        $ownedStmt = $pdo->prepare('SELECT id,title FROM listings WHERE user_id = :uid ORDER BY created_at DESC');
        $ownedStmt->execute([':uid'=>\current_user()['id']]);
        $owned = $ownedStmt->fetchAll();

        $bookedStmt = $pdo->prepare('SELECT l.id,l.title FROM reservations r JOIN listings l ON l.id=r.listing_id WHERE r.renter_id = :rid GROUP BY l.id,l.title ORDER BY MIN(r.created_at) DESC');
        $bookedStmt->execute([':rid'=>\current_user()['id']]);
        $booked = $bookedStmt->fetchAll();

        require __DIR__.'/../Views/messages/index.php';
    }
}

?>
