<?php
namespace App\Controllers;

class AdminController
{
    public function index(): void
    {
        if (!\is_admin()) {
            $error = 'Accès réservé à l’admin.';
            require __DIR__.'/../Views/admin/index.php';
            return;
        }

        $pdo = \app_pdo();
        $errors = []; $info = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!\verify_csrf($_POST['csrf'] ?? '')) { $errors[]='Token CSRF invalide.'; }
            $action = $_POST['action'] ?? '';
            if (!$errors) {
                if ($action === 'delete_user') {
                    $uid = (int)($_POST['user_id'] ?? 0);
                    if ($uid === \current_user()['id']) { $errors[]='Impossible de supprimer votre propre compte admin.'; }
                    else {
                        $pdo->prepare('DELETE FROM users WHERE id = :id')->execute([':id'=>$uid]);
                        $info='Utilisateur supprimé.';
                    }
                } elseif ($action === 'delete_listing') {
                    $lid = (int)($_POST['listing_id'] ?? 0);
                    $pdo->prepare('DELETE FROM listings WHERE id = :id')->execute([':id'=>$lid]);
                    $info='Annonce supprimée.';
                }
            }
        }

        $users = $pdo->query('SELECT id,name,email,is_admin,created_at FROM users ORDER BY created_at DESC')->fetchAll();
        $listings = $pdo->query('SELECT l.id,l.title,l.location,u.name as owner FROM listings l JOIN users u ON u.id=l.user_id ORDER BY l.created_at DESC')->fetchAll();

        require __DIR__.'/../Views/admin/index.php';
    }
}

?>
