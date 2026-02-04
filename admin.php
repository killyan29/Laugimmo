<?php
// Front controller MVC pour l'administration
require_once __DIR__.'/app/bootstrap.php';
use App\Controllers\AdminController;
$controller = new AdminController();
$controller->index();
exit;
?>
<?php require_once __DIR__.'/includes/header.php'; if (!is_admin()) { echo '<p>Accès réservé à l’admin.</p>'; require_once __DIR__.'/includes/footer.php'; exit; } $pdo = get_pdo();
$info='';$errors=[];
if ($_SERVER['REQUEST_METHOD']==='POST') {
  if (!verify_csrf($_POST['csrf'] ?? '')) { $errors[]='Token CSRF invalide.'; }
  $action = $_POST['action'] ?? '';
  if (!$errors) {
    if ($action==='delete_user') {
      $uid = (int)($_POST['user_id'] ?? 0);
      if ($uid===current_user()['id']) { $errors[]='Impossible de supprimer votre propre compte admin.'; }
      else {
        $pdo->prepare('DELETE FROM users WHERE id = :id')->execute([':id'=>$uid]);
        $info='Utilisateur supprimé.';
      }
    } elseif ($action==='delete_listing') {
      $lid = (int)($_POST['listing_id'] ?? 0);
      $pdo->prepare('DELETE FROM listings WHERE id = :id')->execute([':id'=>$lid]);
      $info='Annonce supprimée.';
    }
  }
}
$users = $pdo->query('SELECT id,name,email,is_admin,created_at FROM users ORDER BY created_at DESC')->fetchAll();
$listings = $pdo->query('SELECT l.id,l.title,l.location,u.name as owner FROM listings l JOIN users u ON u.id=l.user_id ORDER BY l.created_at DESC')->fetchAll();
?>
<h1>Administration</h1>
<?php foreach($errors as $e): ?><div class="alert error"><?php echo h($e); ?></div><?php endforeach; ?>
<?php if ($info): ?><div class="alert success"><?php echo h($info); ?></div><?php endif; ?>

<section class="form">
  <h2>Utilisateurs</h2>
  <?php foreach($users as $u): ?>
    <div style="display:flex;justify-content:space-between;align-items:center;padding:6px 0;border-bottom:1px solid #eee">
      <div><?php echo h($u['name']); ?> (<?php echo h($u['email']); ?>) <?php echo $u['is_admin']? '• Admin':''; ?></div>
      <form method="post" onsubmit="return confirm('Supprimer cet utilisateur ?');">
        <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
        <input type="hidden" name="action" value="delete_user">
        <input type="hidden" name="user_id" value="<?php echo (int)$u['id']; ?>">
        <button type="submit">Supprimer</button>
      </form>
    </div>
  <?php endforeach; ?>
</section>

<section class="form" style="margin-top:16px">
  <h2>Annonces</h2>
  <?php foreach($listings as $l): ?>
    <div style="display:flex;justify-content:space-between;align-items:center;padding:6px 0;border-bottom:1px solid #eee">
      <div><?php echo h($l['title']); ?> – <?php echo h($l['location']); ?> (<?php echo h($l['owner']); ?>)</div>
      <form method="post" onsubmit="return confirm('Supprimer cette annonce ?');">
        <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
        <input type="hidden" name="action" value="delete_listing">
        <input type="hidden" name="listing_id" value="<?php echo (int)$l['id']; ?>">
        <button type="submit">Supprimer</button>
      </form>
    </div>
  <?php endforeach; ?>
</section>
<?php require_once __DIR__.'/includes/footer.php'; ?>
