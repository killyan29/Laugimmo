<?php require_once __DIR__.'/../../../includes/header.php'; ?>
<?php \require_login(); ?>

<h1>Mon espace</h1>
<p>Bienvenue, <?php echo h(current_user()['name']); ?>.</p>

<div class="form" style="margin-top:12px">
  <div class="form-row">
    <div class="field">
      <label>Mes statistiques</label>
      <div>Mes annonces: <?php echo (int)($stats['listings'] ?? 0); ?></div>
      <div>Mes réservations: <?php echo (int)($stats['reservations'] ?? 0); ?></div>
    </div>
  </div>
</div>

<h2 style="margin-top:18px">Actions rapides</h2>
<div class="chip-group">
  <a class="chip" href="create_listing.php">➕ Créer une annonce</a>
  <a class="chip" href="my_listings.php">📦 Mes annonces</a>
  <a class="chip" href="my_reservations.php">🧾 Mes réservations</a>
  <a class="chip" href="messages.php">💬 Messages</a>
  <?php if (is_admin()): ?><a class="chip" href="admin.php">🛡️ Admin</a><?php endif; ?>
</div>

<?php require_once __DIR__.'/../../../includes/footer.php'; ?>

