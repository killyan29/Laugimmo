<?php require_once __DIR__.'/../../../includes/header.php'; ?>
<?php \require_login(); ?>
<h1>Mes réservations</h1>
<?php if (!($reservations ?? [])): ?>
  <p>Vous n’avez pas encore de réservations.</p>
<?php else: ?>
  <div class="card-grid">
    <?php foreach($reservations as $r): ?>
      <article class="card">
        <div class="content">
          <h2 class="title"><?php echo h($r['title']); ?></h2>
          <div class="meta">Lieu: <?php echo h($r['location']); ?></div>
          <div class="meta">Du <?php echo h($r['start_date']); ?> au <?php echo h($r['end_date']); ?></div>
          <div class="price">Total: €<?php echo number_format((float)$r['total_price'],2,',',' '); ?></div>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
<?php require_once __DIR__.'/../../../includes/footer.php'; ?>

