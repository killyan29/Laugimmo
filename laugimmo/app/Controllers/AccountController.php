<?php
namespace App\Controllers;

class AccountController
{
    public function index(): void
    {
        \require_login();
        $user = \current_user();

        // On peut charger des infos supplémentaires si nécessaire
        $pdo = \app_pdo();
        $stats = [
            'listings' => (int)$pdo->query("SELECT COUNT(*) FROM listings WHERE user_id = ".$pdo->quote((int)$user['id']))->fetchColumn(),
            'reservations' => (int)$pdo->query("SELECT COUNT(*) FROM reservations WHERE renter_id = ".$pdo->quote((int)$user['id']))->fetchColumn(),
        ];

        require __DIR__.'/../Views/account/index.php';
    }
}

?>
