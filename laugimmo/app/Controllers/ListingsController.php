<?php
namespace App\Controllers;

use App\Models\Listing;

class ListingsController
{
    public function index(): void
    {
        // Récupération des filtres depuis la requête
        $filters = [
            'location'   => $_GET['location'] ?? '',
            'min_rooms'  => $_GET['min_rooms'] ?? 0,
            'min_price'  => $_GET['min_price'] ?? 0,
            'max_price'  => $_GET['max_price'] ?? 0,
            'has_pool'   => isset($_GET['has_pool']) ? 1 : null,
            'category'   => $_GET['category'] ?? '',
        ];

        $listings = Listing::all($filters);

        // Variables exposées à la vue
        $location   = trim((string)$filters['location']);
        $min_rooms  = (int)$filters['min_rooms'];
        $min_price  = (float)$filters['min_price'];
        $max_price  = (float)$filters['max_price'];
        $has_pool   = $filters['has_pool'] ? 1 : null;
        $category   = trim((string)$filters['category']);

        // Afficher la vue
        require __DIR__.'/../Views/listings/index.php';
    }

    public function show(): void
    {
        $pdo = \app_pdo();
        $id = (int)($_GET['id'] ?? 0);

        $stmt = $pdo->prepare('SELECT l.*, u.id as owner_id, u.name as owner_name FROM listings l JOIN users u ON u.id = l.user_id WHERE l.id = :id');
        $stmt->execute([':id'=>$id]);
        $listing = $stmt->fetch();
        if (!$listing) {
            // Vue simple d’erreur via la vue
            $error = 'Annonce introuvable.';
            require __DIR__.'/../Views/listings/show.php';
            return;
        }

        // Photos
        $photosStmt = $pdo->prepare('SELECT * FROM listing_photos WHERE listing_id = :id ORDER BY id ASC');
        $photosStmt->execute([':id'=>$id]);
        $photos = $photosStmt->fetchAll();

        // Réservation
        $reserve_success=''; $reserve_errors=[];
        if (\is_logged_in() && (($_POST['action'] ?? '')==='reserve')) {
            if (!\verify_csrf($_POST['csrf'] ?? '')) { $reserve_errors[]='Token CSRF invalide.'; }
            $start = $_POST['start_date'] ?? '';
            $end = $_POST['end_date'] ?? '';
            $start_ts = strtotime($start); $end_ts = strtotime($end);
            if (!$start_ts || !$end_ts || $end_ts <= $start_ts) { $reserve_errors[]='Dates invalides.'; }
            if (!$reserve_errors) {
                $q = $pdo->prepare('SELECT COUNT(*) FROM reservations WHERE listing_id = :lid AND NOT (end_date <= :start OR start_date >= :end)');
                $q->execute([':lid'=>$id, ':start'=>$start, ':end'=>$end]);
                $overlaps = (int)$q->fetchColumn();
                if ($overlaps>0) { $reserve_errors[]='Ces dates ne sont pas disponibles.'; }
            }
            if (!$reserve_errors) {
                $nights = max(1, (int)ceil(($end_ts - $start_ts) / 86400));
                $total = $nights * (float)$listing['price_per_night'];
                $ins = $pdo->prepare('INSERT INTO reservations (listing_id,renter_id,start_date,end_date,total_price,created_at) VALUES (:lid,:rid,:sd,:ed,:tot,NOW())');
                $ins->execute([':lid'=>$id, ':rid'=>\current_user()['id'], ':sd'=>$start, ':ed'=>$end, ':tot'=>$total]);
                $reserve_success = 'Réservation confirmée pour '.$nights.' nuit(s). Total: €'.number_format($total,2,',',' ');
            }
        }

        // Messagerie
        $msg_errors=[];$msg_success='';
        if (\is_logged_in() && (($_POST['action'] ?? '')==='message')) {
            if (!\verify_csrf($_POST['csrf'] ?? '')) { $msg_errors[]='Token CSRF invalide.'; }
            $body = trim($_POST['body'] ?? '');
            if ($body==='') { $msg_errors[]='Message vide.'; }
            $receiver_id = (\current_user()['id']==$listing['owner_id']) ? null : $listing['owner_id'];
            if (!$msg_errors) {
                $pdo->prepare('INSERT INTO messages (listing_id,sender_id,receiver_id,body,created_at) VALUES (:lid,:sid,:rid,:b,NOW())')
                    ->execute([':lid'=>$id, ':sid'=>\current_user()['id'], ':rid'=>$receiver_id ?? $listing['owner_id'], ':b'=>$body]);
                $msg_success='Message envoyé.';
            }
        }
        $threadStmt = $pdo->prepare('SELECT m.*, u.name as sender_name FROM messages m JOIN users u ON u.id = m.sender_id WHERE m.listing_id = :lid ORDER BY m.created_at ASC');
        $threadStmt->execute([':lid'=>$id]);
        $thread = $threadStmt->fetchAll();

        // Rendu
        require __DIR__.'/../Views/listings/show.php';
    }

    public function create(): void
    {
        \require_login();
        $pdo = \app_pdo();
        $errors = []; $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!\verify_csrf($_POST['csrf'] ?? '')) { $errors[] = 'Token CSRF invalide.'; }
            $title = trim($_POST['title'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $price = (float)($_POST['price'] ?? 0);
            $rooms = (int)($_POST['rooms'] ?? 0);
            $location = trim($_POST['location'] ?? '');
            $category = trim($_POST['category'] ?? 'maison');
            $has_pool = isset($_POST['has_pool']) ? 1 : 0;

            if ($title==='') $errors[]='Titre requis';
            if ($price<=0) $errors[]='Prix incorrect';
            if ($rooms<=0) $errors[]='Nombre de pièces incorrect';
            if ($location==='') $errors[]='Localisation requise';

            if (!$errors) {
                $stmt = $pdo->prepare('INSERT INTO listings (user_id,title,description,price_per_night,rooms,location,category,has_pool,created_at) VALUES (:uid,:t,:d,:p,:r,:loc,:cat,:pool,NOW())');
                $stmt->execute([':uid'=>\current_user()['id'],':t'=>$title,':d'=>$description,':p'=>$price,':r'=>$rooms,':loc'=>$location,':cat'=>$category,':pool'=>$has_pool]);
                $listing_id = (int)$pdo->lastInsertId();

                if (!empty($_FILES['photos']['name'][0])) {
                    $allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
                    for ($i=0; $i<count($_FILES['photos']['name']); $i++) {
                        $tmp = $_FILES['photos']['tmp_name'][$i];
                        $type = $_FILES['photos']['type'][$i];
                        if (!is_uploaded_file($tmp) || !isset($allowed[$type])) continue;
                        $ext = $allowed[$type];
                        $safe = $listing_id.'_'.time().'_'.bin2hex(random_bytes(4)).'.'.$ext;
                        if (!is_dir(__DIR__.'/../../uploads')) { mkdir(__DIR__.'/../../uploads',0775,true); }
                        move_uploaded_file($tmp, __DIR__.'/../../uploads/'.$safe);
                        $pdo->prepare('INSERT INTO listing_photos (listing_id,file_path) VALUES (:lid,:fp)')->execute([':lid'=>$listing_id, ':fp'=>$safe]);
                    }
                }
                $success = 'Annonce créée !';
            }
        }

        require __DIR__.'/../Views/listings/create.php';
    }

    public function edit(): void
    {
        \require_login();
        $pdo = \app_pdo();
        $id = (int)($_GET['id'] ?? 0);
        $errors=[]; $info='';

        $stmt = $pdo->prepare('SELECT * FROM listings WHERE id = :id');
        $stmt->execute([':id'=>$id]);
        $listing = $stmt->fetch();
        if (!$listing || (int)$listing['user_id'] !== (int)\current_user()['id']) {
            $error='Annonce introuvable ou non autorisée.';
            require __DIR__.'/../Views/listings/edit.php';
            return;
        }

        if ($_SERVER['REQUEST_METHOD']==='POST') {
            if (!\verify_csrf($_POST['csrf'] ?? '')) { $errors[]='Token CSRF invalide.'; }
            $action = $_POST['action'] ?? 'update';
            if ($action==='update' && !$errors) {
                $title = trim($_POST['title'] ?? '');
                $description = trim($_POST['description'] ?? '');
                $price = (float)($_POST['price'] ?? 0);
                $rooms = (int)($_POST['rooms'] ?? 0);
                $location = trim($_POST['location'] ?? '');
                $category = trim($_POST['category'] ?? 'maison');
                $has_pool = isset($_POST['has_pool']) ? 1 : 0;
                if ($title==='') $errors[]='Titre requis';
                if ($price<=0) $errors[]='Prix incorrect';
                if ($rooms<=0) $errors[]='Nombre de pièces incorrect';
                if ($location==='') $errors[]='Localisation requise';
                if (!$errors) {
                    $pdo->prepare('UPDATE listings SET title=:t,description=:d,price_per_night=:p,rooms=:r,location=:loc,category=:cat,has_pool=:pool WHERE id=:id')
                        ->execute([':t'=>$title,':d'=>$description,':p'=>$price,':r'=>$rooms,':loc'=>$location,':cat'=>$category,':pool'=>$has_pool,':id'=>$id]);
                    if (!empty($_FILES['photos']['name'][0])) {
                        $allowed = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
                        for ($i=0; $i<count($_FILES['photos']['name']); $i++) {
                            $tmp = $_FILES['photos']['tmp_name'][$i];
                            $type = $_FILES['photos']['type'][$i];
                            if (!is_uploaded_file($tmp) || !isset($allowed[$type])) continue;
                            $ext = $allowed[$type];
                            $safe = $id.'_'.time().'_'.bin2hex(random_bytes(4)).'.'.$ext;
                            if (!is_dir(__DIR__.'/../../uploads')) { mkdir(__DIR__.'/../../uploads',0775,true); }
                            move_uploaded_file($tmp, __DIR__.'/../../uploads/'.$safe);
                            $pdo->prepare('INSERT INTO listing_photos (listing_id,file_path) VALUES (:lid,:fp)')->execute([':lid'=>$id, ':fp'=>$safe]);
                        }
                    }
                    $info='Annonce mise à jour.';
                    $stmt->execute([':id'=>$id]);
                    $listing = $stmt->fetch();
                }
            } elseif ($action==='delete_photo' && !$errors) {
                $pid = (int)($_POST['photo_id'] ?? 0);
                $ph = $pdo->prepare('SELECT file_path FROM listing_photos WHERE id=:pid AND listing_id=:lid');
                $ph->execute([':pid'=>$pid, ':lid'=>$id]);
                $row = $ph->fetch();
                if ($row) {
                    $path = __DIR__.'/../../uploads/'.$row['file_path'];
                    if (is_file($path)) { @unlink($path); }
                    $pdo->prepare('DELETE FROM listing_photos WHERE id=:pid')->execute([':pid'=>$pid]);
                    $info='Photo supprimée.';
                }
            }
        }

        $photosStmt = $pdo->prepare('SELECT * FROM listing_photos WHERE listing_id = :id ORDER BY id ASC');
        $photosStmt->execute([':id'=>$id]);
        $photos = $photosStmt->fetchAll();

        // Réservations récentes
        $resStmt = $pdo->prepare('SELECT * FROM reservations WHERE listing_id = :id ORDER BY created_at DESC LIMIT 6');
        $resStmt->execute([':id'=>$id]);
        $res = $resStmt->fetchAll();

        require __DIR__.'/../Views/listings/edit.php';
    }

    public function mine(): void
    {
        \require_login();
        $pdo = \app_pdo();
        $errors = []; $info = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && (($_POST['action'] ?? '') === 'delete_listing')) {
            if (!\verify_csrf($_POST['csrf'] ?? '')) { $errors[] = 'Token CSRF invalide.'; }
            $lid = (int)($_POST['listing_id'] ?? 0);
            $ownerStmt = $pdo->prepare('SELECT user_id FROM listings WHERE id = :id');
            $ownerStmt->execute([':id'=>$lid]);
            $row = $ownerStmt->fetch();
            if (!$row || (int)$row['user_id'] !== (int)\current_user()['id']) {
                $errors[] = 'Vous ne pouvez pas supprimer cette annonce.';
            } else if (!$errors) {
                $ph = $pdo->prepare('SELECT file_path FROM listing_photos WHERE listing_id = :id');
                $ph->execute([':id'=>$lid]);
                foreach ($ph->fetchAll() as $p) {
                    $path = __DIR__.'/../../uploads/'.$p['file_path'];
                    if (is_file($path)) { @unlink($path); }
                }
                $pdo->prepare('DELETE FROM listings WHERE id = :id')->execute([':id'=>$lid]);
                $info = 'Annonce supprimée.';
            }
        }

        $stmt = $pdo->prepare("SELECT l.*, 
         (SELECT COUNT(*) FROM reservations r WHERE r.listing_id = l.id) AS res_count,
         (SELECT COALESCE(SUM(total_price),0) FROM reservations r WHERE r.listing_id = l.id) AS revenue,
         (SELECT MIN(start_date) FROM reservations r WHERE r.listing_id = l.id AND r.start_date >= CURDATE()) AS next_start,
         (SELECT file_path FROM listing_photos p WHERE p.listing_id = l.id ORDER BY id ASC LIMIT 1) AS cover
         FROM listings l WHERE l.user_id = :uid ORDER BY l.created_at DESC");
        $stmt->execute([':uid'=>\current_user()['id']]);
        $listings = $stmt->fetchAll();

        require __DIR__.'/../Views/listings/mine.php';
    }
}

?>
