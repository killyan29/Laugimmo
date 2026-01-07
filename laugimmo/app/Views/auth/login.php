<?php require_once __DIR__.'/../../../includes/header.php'; ?>
<h1>Connexion</h1>
<?php foreach(($errors ?? []) as $err): ?><div class="alert error"><?php echo h($err); ?></div><?php endforeach; ?>
<form class="form" method="post">
  <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
  <div class="form-row">
    <div class="field"><label>Email</label><input type="email" name="email" required></div>
    <div class="field"><label>Mot de passe</label><input type="password" name="password" required></div>
  </div>
  <div style="margin-top:10px"><button type="submit">Se connecter</button></div>
</form>
<p>Pas de compte ? <a href="register.php">Créer un compte</a></p>
<?php require_once __DIR__.'/../../../includes/footer.php'; ?>

