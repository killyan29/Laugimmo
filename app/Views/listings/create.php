<?php require_once __DIR__.'/../../../includes/header.php'; ?>
<h1>Créer une annonce</h1>
<?php foreach(($errors ?? []) as $e): ?><div class="alert error"><?php echo h($e); ?></div><?php endforeach; ?>
<?php if (!empty($success)): ?><div class="alert success"><?php echo h($success); ?></div><?php endif; ?>
<form class="form" method="post" enctype="multipart/form-data">
  <input type="hidden" name="csrf" value="<?php echo csrf_token(); ?>">
  <div class="form-row">
    <div class="field"><label>Titre</label><input type="text" name="title" required></div>
    <div class="field"><label>Prix par nuit (€)</label><input type="number" step="0.01" name="price" required></div>
    <div class="field"><label>Nombre de pièces</label><input type="number" name="rooms" required></div>
    <div class="field"><label>Localisation</label><input type="text" name="location" required></div>
    <div class="field"><label>Catégorie</label>
      <select name="category">
        <?php foreach(['maison','appartement','villa','chalet'] as $cat): ?>
          <option value="<?php echo h($cat); ?>"><?php echo h(ucfirst($cat)); ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="field" style="align-self:flex-end">
      <label><input type="checkbox" name="has_pool"> Piscine</label>
    </div>
  </div>
  <div class="field"><label>Description</label><textarea name="description" required></textarea></div>
  <div class="field"><label>Photos (JPG/PNG/WebP, multiples)</label><input type="file" name="photos[]" multiple accept="image/*"></div>
  <div style="margin-top:10px"><button type="submit">Publier</button></div>
  <div style="margin-top:8px"><a class="chip" href="my_listings.php">Retour à Mes annonces</a></div>
</form>
<?php require_once __DIR__.'/../../../includes/footer.php'; ?>

