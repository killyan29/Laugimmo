<?php require_once __DIR__.'/../../../includes/header.php'; ?>
<?php if (!empty($error)): ?>
  <p><?php echo h($error); ?></p>
  <?php require_once __DIR__.'/../../../includes/footer.php'; return; ?>
<?php endif; ?>

<h1>Modifier l’annonce</h1>
<?php foreach(($errors ?? []) as $e): ?><div class="alert error"><?php echo h($e); ?></div><?php endforeach; ?>
<?php if (!empty($info)): ?><div class="alert success"><?php echo h($info); ?></div><?php endif; ?>
<form class="form" method="post" enctype="multipart/form-data">
  <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
  <input type="hidden" name="action" value="update">
  <div class="form-row">
    <div class="field"><label>Titre</label><input type="text" name="title" value="<?php echo h($listing['title']); ?>" required></div>
    <div class="field"><label>Prix par nuit (€)</label><input type="number" step="0.01" name="price" value="<?php echo h((string)$listing['price_per_night']); ?>" required></div>
    <div class="field"><label>Pièces</label><input type="number" name="rooms" value="<?php echo (int)$listing['rooms']; ?>" required></div>
    <div class="field"><label>Localisation</label><input type="text" name="location" value="<?php echo h($listing['location']); ?>" required></div>
    <div class="field"><label>Catégorie</label>
      <select name="category">
        <?php foreach(['maison','appartement','villa','chalet'] as $cat): ?>
          <option value="<?php echo h($cat); ?>" <?php echo $listing['category']===$cat?'selected':''; ?>><?php echo h(ucfirst($cat)); ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="field" style="align-self:flex-end">
      <label><input type="checkbox" name="has_pool" <?php echo $listing['has_pool']? 'checked':''; ?>> Piscine</label>
    </div>
  </div>
  <div class="field"><label>Description</label><textarea name="description" required><?php echo h($listing['description']); ?></textarea></div>
  <div class="field"><label>Ajouter des photos</label><input type="file" name="photos[]" multiple accept="image/*"></div>
  <div style="margin-top:10px"><button type="submit">Enregistrer</button> <a class="chip" href="my_listings.php">Retour</a></div>
</form>

<h2 style="margin-top:18px">Photos existantes</h2>
<div class="gallery">
<?php foreach($photos as $p): ?>
  <div>
    <?php
      $path = trim((string)($p['file_path'] ?? ''));
      $path = trim($path, " \t\n\r\0\x0B`\"'");
      $is_external = $path !== '' && preg_match('#^https?://#', $path);
      $unsplash_id = null;
      if ($is_external && preg_match('#unsplash\\.com/photos/([A-Za-z0-9_-]+)#', $path, $m)) {
        $unsplash_id = $m[1];
      }
      $src = $path !== ''
        ? ($is_external
            ? ($unsplash_id ? ("https://source.unsplash.com/{$unsplash_id}/600x400") : $path)
            : ('uploads/'.ltrim($path,'/')))
        : '';
    ?>
    <?php if ($src !== ''): ?>
      <img src="<?php echo h($src); ?>" alt="Photo">
    <?php endif; ?>
    <form method="post" style="margin-top:6px" onsubmit="return confirm('Supprimer cette photo ?');">
      <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
      <input type="hidden" name="action" value="delete_photo">
      <input type="hidden" name="photo_id" value="<?php echo (int)$p['id']; ?>">
      <button type="submit" class="chip" style="background:#fee2e2;border-color:#f7b4b4;color:#8a0f0f">🗑️ Supprimer</button>
    </form>
  </div>
<?php endforeach; ?>
<?php if (!$photos): ?>
  <p>Aucune photo pour l’instant.</p>
<?php endif; ?>
</div>

<h2 style="margin-top:18px">Réservations récentes</h2>
<div class="form">
  <?php foreach(($res ?? []) as $r): ?>
    <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #eee">
      <div>Du <?php echo h($r['start_date']); ?> au <?php echo h($r['end_date']); ?></div>
      <div>€<?php echo number_format((float)$r['total_price'],2,',',' '); ?></div>
    </div>
  <?php endforeach; ?>
  <?php if (!($res ?? [])): ?><p>Aucune réservation enregistrée.</p><?php endif; ?>
</div>
<?php require_once __DIR__.'/../../../includes/footer.php'; ?>
