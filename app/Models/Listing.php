<?php
namespace App\Models;

use PDO;

class Listing
{
    public static function all(array $filters = []): array
    {
        $pdo = \app_pdo();

        $location = trim($filters['location'] ?? '');
        $min_rooms = (int)($filters['min_rooms'] ?? 0);
        $min_price = (float)($filters['min_price'] ?? 0);
        $max_price = (float)($filters['max_price'] ?? 0);
        $has_pool = isset($filters['has_pool']) ? 1 : null;
        $category = trim($filters['category'] ?? '');

        $where = [];
        $params = [];
        if ($location !== '') { $where[] = 'l.location LIKE :location'; $params[':location'] = "%$location%"; }
        if ($min_rooms > 0) { $where[] = 'l.rooms >= :rooms'; $params[':rooms'] = $min_rooms; }
        if ($min_price > 0) { $where[] = 'l.price_per_night >= :min_price'; $params[':min_price'] = $min_price; }
        if ($max_price > 0) { $where[] = 'l.price_per_night <= :price'; $params[':price'] = $max_price; }
        if ($has_pool !== null) { $where[] = 'l.has_pool = :pool'; $params[':pool'] = $has_pool; }
        if ($category !== '') { $where[] = 'l.category = :category'; $params[':category'] = $category; }

        $sql = "SELECT l.*, u.name as owner_name,
                (SELECT file_path FROM listing_photos p WHERE p.listing_id = l.id ORDER BY id ASC LIMIT 1) as cover
                FROM listings l JOIN users u ON u.id = l.user_id";
        if ($where) { $sql .= ' WHERE '.implode(' AND ', $where); }
        $sql .= ' ORDER BY l.created_at DESC';

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function find(int $id)
    {
        $pdo = \app_pdo();
        $stmt = $pdo->prepare('SELECT l.*, u.id as owner_id, u.name as owner_name FROM listings l JOIN users u ON u.id = l.user_id WHERE l.id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }
}

?>
