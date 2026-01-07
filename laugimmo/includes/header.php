<?php require_once __DIR__.'/../config.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Laugimmo – Locations de maisons</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="site-header">
  <div class="container header-content">
    <a class="logo" href="index.php">Laugimmo</a>
    <nav class="nav">
      <?php if (is_logged_in()): ?>
        <div class="nav-right">
          <details class="nav-dropdown">
            <summary>
              <a href="account.php" class="summary-link">Mon espace</a>
              <span class="caret" aria-hidden="true"></span>
            </summary>
            <div class="dropdown-menu">
              <a href="create_listing.php">Créer une annonce</a>
              <a href="my_listings.php">Mes annonces</a>
              <a href="my_reservations.php">Mes réservations</a>
              <?php if (is_admin()): ?>
                <a href="admin.php">Admin</a>
              <?php endif; ?>
            </div>
          </details>
          <a class="nav-link home" href="index.php">Accueil</a>
          <a class="nav-link messages" href="messages.php">Messages</a>
          <a class="nav-link logout" href="logout.php">Déconnexion</a>
        </div>
      <?php else: ?>
        <a href="index.php">Accueil</a>
        <a href="login.php">Connexion</a>
        <a href="register.php">Inscription</a>
      <?php endif; ?>

    </nav>
  </div>
</header>
<main class="container main-content">
