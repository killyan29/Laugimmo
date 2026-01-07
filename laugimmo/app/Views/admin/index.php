<?php require_once __DIR__.'/../../../includes/header.php'; ?>
<?php if (!is_admin()): ?>
  <h1>Administration</h1>
  <p>Accès refusé. Réservé aux administrateurs.</p>
  <?php require_once __DIR__.'/../../../includes/footer.php'; return; endif; ?>

<h1>Administration</h1>
<?php if (!empty($errors)): ?>
  <div class="alert alert-error">
    <?php foreach($errors as $e): ?><p><?php echo h($e); ?></p><?php endforeach; ?>
  </div>
<?php endif; ?>
<?php if (!empty($info)): ?>
  <div class="alert alert-success"><p><?php echo h($info); ?></p></div>
<?php endif; ?>

<section>
  <h2>Utilisateurs</h2>
  <table class="table">
    <thead><tr><th>Nom</th><th>Email</th><th>Admin</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach(($users ?? []) as $u): ?>
        <tr>
          <td><?php echo h($u['name']); ?></td>
          <td><?php echo h($u['email']); ?></td>
          <td><?php echo $u['is_admin'] ? 'Oui' : 'Non'; ?></td>
          <td>
            <form method="post" onsubmit="return confirm('Supprimer cet utilisateur ?');">
              <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
              <input type="hidden" name="action" value="delete_user">
              <input type="hidden" name="user_id" value="<?php echo (int)$u['id']; ?>">
              <button class="btn btn-danger" <?php echo ($u['id']===current_user()['id'])?'disabled':''; ?>>Supprimer</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>

<section>
  <h2>Annonces</h2>
  <table class="table">
    <thead><tr><th>Titre</th><th>Lieu</th><th>Propriétaire</th><th>Actions</th></tr></thead>
    <tbody>
      <?php foreach(($listings ?? []) as $l): ?>
        <tr>
          <td><a href="listing.php?id=<?php echo (int)$l['id']; ?>"><?php echo h($l['title']); ?></a></td>
          <td><?php echo h($l['location'] ?? ''); ?></td>
          <td><?php echo h($l['owner']); ?></td>
          <td>
            <form method="post" onsubmit="return confirm('Supprimer cette annonce ?');">
              <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
              <input type="hidden" name="action" value="delete_listing">
              <input type="hidden" name="listing_id" value="<?php echo (int)$l['id']; ?>">
              <button class="btn btn-danger">Supprimer</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>

<?php require_once __DIR__.'/../../../includes/footer.php'; ?>

