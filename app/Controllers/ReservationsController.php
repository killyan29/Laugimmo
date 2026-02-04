<?php
namespace App\Controllers;

class ReservationsController
{
    public function mine(): void
    {
        \require_login();
        $pdo = \app_pdo();
        $stmt = $pdo->prepare('SELECT r.*, l.title, l.location FROM reservations r JOIN listings l ON l.id = r.listing_id WHERE r.renter_id = :rid ORDER BY r.created_at DESC');
        $stmt->execute([':rid'=>\current_user()['id']]);
        $reservations = $stmt->fetchAll();

        require __DIR__.'/../Views/reservations/mine.php';
    }
}

?>
