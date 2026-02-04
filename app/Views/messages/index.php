<?php require_once __DIR__.'/../../../includes/header.php'; ?>
<?php \require_login(); ?>
<h1>Messages par annonce</h1>
<p>Sélectionnez une annonce pour discuter avec la contrepartie.</p>
<div class="card-grid">
  <?php foreach(array_merge(($owned ?? []), ($booked ?? [])) as $l): ?>
    <article class="card">
      <div class="content">
        <h2 class="title"><a href="listing.php?id=<?php echo (int)$l['id']; ?>"><?php echo h($l['title']); ?></a></h2>
        <div class="meta">Ouvrir le fil de discussion</div>
      </div>
    </article>
  <?php endforeach; ?>
  <?php if (!(($owned ?? []) || ($booked ?? []))): ?>
    <p>Aucune discussion pour l’instant.</p>
  <?php endif; ?>
</div>
<?php require_once __DIR__.'/../../../includes/footer.php'; ?>

