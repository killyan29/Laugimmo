<?php require_once __DIR__.'/../../../includes/header.php'; ?>
<?php \require_login(); ?>
<h1>Mes annonces</h1>
<?php foreach(($errors ?? []) as $e): ?><div class="alert error"><?php echo h($e); ?></div><?php endforeach; ?>
<?php if (!empty($info)): ?><div class="alert success"><?php echo h($info); ?></div><?php endif; ?>
<p>Gérez vos annonces: modifiez, supprimez, consultez les réservations et statistiques.</p>
<div class="card-grid">
<?php foreach(($listings ?? []) as $l): ?>
  <article class="card">
    <a href="listing.php?id=<?php echo (int)$l['id']; ?>">
      <?php
        $cover = trim((string)($l['cover'] ?? ''));
        $cover = trim($cover, " \t\n\r\0\x0B`\"'");
        $is_external = $cover !== '' && preg_match('#^https?://#', $cover);
        $unsplash_id = null;
        if ($is_external && preg_match('#unsplash\\.com/photos/([A-Za-z0-9_-]+)#', $cover, $m)) {
          $unsplash_id = $m[1];
        }
        // Pour Unsplash, utiliser l’endpoint de téléchargement direct (redirige vers images.unsplash.com)
        $src = $cover !== ''
          ? ($is_external
              ? ($unsplash_id ? ("https://unsplash.com/photos/{$unsplash_id}/download?force=true") : $cover)
              : ('uploads/'.ltrim($cover,'/')))
          : ('https://picsum.photos/seed/'.(int)$l['id'].'/600/400');
      ?>
      <img
        src="<?php echo h($src); ?>"
        alt="Cover"
        loading="lazy"
        decoding="async"
        referrerpolicy="no-referrer"
        onerror="this.onerror=null; this.src='<?php echo 'https://picsum.photos/seed/'.(int)$l['id'].'/600/400'; ?>';"
      >
    </a>
    <div class="content">
      <h2 class="title"><?php echo h($l['title']); ?></h2>
      <div class="meta"><?php echo h($l['location']); ?> • <?php echo (int)$l['rooms']; ?> pièces</div>
      <div class="badge badge-cat"><?php echo h(ucfirst($l['category'])); ?></div>
      <div class="price">€<?php echo number_format((float)$l['price_per_night'],2,',',' '); ?> / nuit</div>
      <div class="meta">Réservations: <?php echo (int)$l['res_count']; ?> • Prochaine: <?php echo $l['next_start']? h($l['next_start']) : '—'; ?> • Revenu: €<?php echo number_format((float)$l['revenue'],2,',',' '); ?></div>
      <div style="display:flex;gap:8px;margin-top:10px">
        <a class="chip" href="edit_listing.php?id=<?php echo (int)$l['id']; ?>">✏️ Modifier</a>
        <a class="chip" href="listing.php?id=<?php echo (int)$l['id']; ?>">🔍 Voir</a>
        <form method="post" onsubmit="return confirm('Supprimer cette annonce ?');">
          <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
          <input type="hidden" name="action" value="delete_listing">
          <input type="hidden" name="listing_id" value="<?php echo (int)$l['id']; ?>">
          <button class="chip" type="submit" style="background:#fee2e2;border-color:#f7b4b4;color:#8a0f0f">🗑️ Supprimer</button>
        </form>
      </div>
    </div>
  </article>
<?php endforeach; ?>
<?php if (!($listings ?? [])): ?>
  <p>Vous n’avez pas encore d’annonces. <a href="create_listing.php">Créez votre première annonce</a>.</p>
<?php endif; ?>
</div>
<?php require_once __DIR__.'/../../../includes/footer.php'; ?>
