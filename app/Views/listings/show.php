<?php require_once __DIR__.'/../../../includes/header.php'; ?>
<?php if (!empty($error)): ?>
  <p><?php echo h($error); ?></p>
  <?php require_once __DIR__.'/../../../includes/footer.php'; return; ?>
<?php endif; ?>

<article>
  <h1><?php echo h($listing['title']); ?></h1>
  <div class="meta">Hôte: <?php echo h($listing['owner_name']); ?> • <?php echo h($listing['location']); ?> • <?php echo (int)$listing['rooms']; ?> pièces • <?php echo h(ucfirst($listing['category'])); ?> <?php echo $listing['has_pool']? '• Piscine':''; ?></div>
  <div class="price" style="margin:8px 0">€<?php echo number_format((float)$listing['price_per_night'],2,',',' '); ?> / nuit</div>
  <div class="gallery">
    <?php foreach($photos as $p): ?>
      <?php
        $path = trim((string)($p['file_path'] ?? ''));
        $path = trim($path, " \t\n\r\0\x0B`\"'");
        $is_external = $path !== '' && preg_match('#^https?://#', $path);
        $unsplash_id = null;
        if ($is_external && preg_match('#unsplash\\.com/photos/([A-Za-z0-9_-]+)#', $path, $m)) {
          $unsplash_id = $m[1];
        }
        // Pour Unsplash, utiliser l’endpoint de téléchargement direct (redirige vers images.unsplash.com)
        $src = $path !== ''
          ? ($is_external
              ? ($unsplash_id ? ("https://unsplash.com/photos/{$unsplash_id}/download?force=true") : $path)
              : ('uploads/'.ltrim($path,'/')))
          : '';
      ?>
      <?php if ($src !== ''): ?>
        <img
          src="<?php echo h($src); ?>"
          alt="Photo"
          loading="lazy"
          decoding="async"
          referrerpolicy="no-referrer"
          onerror="this.onerror=null; this.src='https://picsum.photos/seed/<?php echo (int)$listing['id']; ?>/800/500';"
        >
      <?php endif; ?>
    <?php endforeach; ?>
    <?php if (!$photos): ?>
      <img src="https://picsum.photos/seed/<?php echo (int)$listing['id']; ?>/800/500" alt="Placeholder">
    <?php endif; ?>
  </div>
  <p style="margin-top:12px"><?php echo nl2br(h($listing['description'])); ?></p>

  <div class="form" style="margin-top:16px">
    <h2>Réserver cette maison</h2>
    <?php foreach($reserve_errors as $e): ?><div class="alert error"><?php echo h($e); ?></div><?php endforeach; ?>
    <?php if ($reserve_success): ?><div class="alert success"><?php echo h($reserve_success); ?></div><?php endif; ?>
    <?php if (is_logged_in()): ?>
      <form method="post">
        <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
        <input type="hidden" name="action" value="reserve">
        <div class="form-row">
          <div class="field"><label>Arrivée</label><input type="date" name="start_date" required></div>
          <div class="field"><label>Départ</label><input type="date" name="end_date" required></div>
        </div>
        <button type="submit">Réserver</button>
      </form>
    <?php else: ?>
      <p><a href="login.php">Connectez-vous</a> pour réserver.</p>
    <?php endif; ?>
  </div>

  <div class="form" style="margin-top:16px">
    <h2>Messages</h2>
    <?php foreach($msg_errors as $e): ?><div class="alert error"><?php echo h($e); ?></div><?php endforeach; ?>
    <?php if ($msg_success): ?><div class="alert success"><?php echo h($msg_success); ?></div><?php endif; ?>
    <div style="margin-bottom:8px">
      <?php foreach($thread as $m): ?>
        <p><strong><?php echo h($m['sender_name']); ?>:</strong> <?php echo nl2br(h($m['body'])); ?> <span style="color:#777;font-size:12px">(<?php echo h($m['created_at']); ?>)</span></p>
      <?php endforeach; ?>
      <?php if (!$thread): ?><p>Aucun message pour cette annonce.</p><?php endif; ?>
    </div>
    <?php if (is_logged_in()): ?>
      <form method="post">
        <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
        <input type="hidden" name="action" value="message">
        <div class="field"><label>Votre message</label><textarea name="body" required></textarea></div>
        <button type="submit">Envoyer</button>
      </form>
    <?php else: ?>
      <p><a href="login.php">Connectez-vous</a> pour discuter avec l’hôte.</p>
    <?php endif; ?>
  </div>
</article>
<?php require_once __DIR__.'/../../../includes/footer.php'; ?>
