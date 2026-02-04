<?php require_once __DIR__.'/../../../includes/header.php'; ?>
<h1>Inscription</h1>
<?php foreach(($errors ?? []) as $err): ?><div class="alert error"><?php echo h($err); ?></div><?php endforeach; ?>
<?php if (!empty($success)): ?><div class="alert success"><?php echo h($success); ?></div><?php endif; ?>
<form class="form" method="post">
  <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
  <div class="form-row">
    <div class="field"><label>Nom</label><input type="text" name="name" required></div>
    <div class="field"><label>Email</label><input type="email" name="email" required></div>
    <div class="field"><label>Mot de passe</label><input type="password" name="password" required></div>
    <div class="field"><label>Confirmer</label><input type="password" name="confirm" required></div>
  </div>
  <div style="margin-top:10px"><button type="submit">Créer le compte</button></div>
</form>
<?php require_once __DIR__.'/../../../includes/footer.php'; ?>

