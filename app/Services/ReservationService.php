<?php
namespace App\Services;

use PDO;

class ReservationService
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(int $listingId, int $renterId, string $startDate, string $endDate): array
    {
        $errors = [];
        
        // 1. Check if user is logged in (renterId must be valid > 0)
        if ($renterId <= 0) {
            return ['Utilisateur non connecté.'];
        }

        // 2. Check if listing exists and get price
        $stmt = $this->pdo->prepare('SELECT price_per_night FROM listings WHERE id = :id');
        $stmt->execute([':id' => $listingId]);
        $listing = $stmt->fetch();

        if (!$listing) {
            return ['Annonce introuvable.'];
        }

        $start_ts = strtotime($startDate);
        $end_ts = strtotime($endDate);

        if (!$start_ts || !$end_ts || $end_ts <= $start_ts) {
            return ['Dates invalides.'];
        }

        // 3. Check for double booking
        $q = $this->pdo->prepare('SELECT COUNT(*) FROM reservations WHERE listing_id = :lid AND NOT (end_date <= :start OR start_date >= :end)');
        $q->execute([':lid' => $listingId, ':start' => $startDate, ':end' => $endDate]);
        $overlaps = (int)$q->fetchColumn();

        if ($overlaps > 0) {
            return ['Ces dates ne sont pas disponibles.'];
        }

        // 4. Save reservation
        $nights = max(1, (int)ceil(($end_ts - $start_ts) / 86400));
        $total = $nights * (float)$listing['price_per_night'];
        $ins = $this->pdo->prepare('INSERT INTO reservations (listing_id,renter_id,start_date,end_date,total_price,created_at) VALUES (:lid,:rid,:sd,:ed,:tot,NOW())');
        $ins->execute([':lid' => $listingId, ':rid' => $renterId, ':sd' => $startDate, ':ed' => $endDate, ':tot' => $total]);

        return []; // No errors means success
    }
}
