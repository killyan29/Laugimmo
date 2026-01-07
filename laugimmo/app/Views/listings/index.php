<?php require_once __DIR__.'/../../../includes/header.php'; ?>
<section class="hero">
  <h1>Trouvez votre maison idéale</h1>
  <p class="subtitle">Filtrez par lieu, pièces, prix ou équipements — Find your dream.</p>
  <form class="hero-search" method="get">
    <div class="field"><label>Localisation</label><input type="text" name="location" value="<?php echo h($location); ?>" placeholder="Ville, quartier..."></div>
    <div class="field"><label>Min. pièces</label><input type="number" name="min_rooms" value="<?php echo h((string)$min_rooms); ?>" min="0"></div>
    <div class="field"><label>Prix min (€)</label><input type="number" step="0.01" name="min_price" value="<?php echo h((string)($min_price ?? 0)); ?>" min="0"></div>
    <div class="field"><label>Prix max (€)</label><input type="number" step="0.01" name="max_price" value="<?php echo h((string)$max_price); ?>" min="0"></div>
    <div class="field" style="align-self:flex-end"><label><input type="checkbox" name="has_pool" <?php echo $has_pool? 'checked':''; ?>> Piscine</label></div>
    <div class="field"><label>Catégorie</label>
      <select name="category">
        <option value="">Toutes</option>
        <?php foreach(['maison','appartement','villa','chalet'] as $cat): ?>
          <option value="<?php echo h($cat); ?>" <?php echo $category===$cat?'selected':''; ?>><?php echo h(ucfirst($cat)); ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div style="align-self:flex-end"><button type="submit">Rechercher</button></div>
  </form>
</section>
<div class="chip-group">
  <a class="chip" href="index.php"><span class="icon">✨</span>Toutes</a>
  <a class="chip" href="index.php?category=maison"><span class="icon">🏠</span>Maison</a>
  <a class="chip" href="index.php?category=appartement"><span class="icon">🏢</span>Appartement</a>
  <a class="chip" href="index.php?category=villa"><span class="icon">🏡</span>Villa</a>
  <a class="chip" href="index.php?category=chalet"><span class="icon">🏔️</span>Chalet</a>
  <a class="chip" href="index.php?has_pool=1"><span class="icon">🏊</span>Piscine</a>
</div>
<section>
  <div class="card-grid">
    <?php foreach($listings as $l): ?>
      <article class="card">
        <a href="listing.php?id=<?php echo (int)$l['id']; ?>">
          <?php
            $cover = trim((string)($l['cover'] ?? ''));
            // Remove accidental wrapping characters (quotes/backticks/spaces)
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
            alt="Photo de couverture"
            loading="lazy"
            decoding="async"
            referrerpolicy="no-referrer"
            onerror="this.onerror=null; this.src='<?php echo 'https://picsum.photos/seed/'.(int)$l['id'].'/600/400'; ?>';"
          >
        </a>
        <div class="content">
          <h2 class="title"><a href="listing.php?id=<?php echo (int)$l['id']; ?>"><?php echo h($l['title']); ?></a></h2>
          <div class="meta"><?php echo h($l['location']); ?> • <?php echo (int)$l['rooms']; ?> pièces</div>
          <div class="badge badge-cat"><?php echo h(ucfirst($l['category'])); ?></div>
          <div class="price">€<?php echo number_format((float)$l['price_per_night'],2,',',' '); ?> / nuit</div>
          <div class="meta">Hôte: <?php echo h($l['owner_name']); ?></div>
        </div>
      </article>
    <?php endforeach; ?>
    <?php if (!$listings): ?>
      <p>Aucune annonce trouvée pour ces critères.</p>
    <?php endif; ?>
  </div>
</section>
<?php require_once __DIR__.'/../../../includes/footer.php'; ?>
