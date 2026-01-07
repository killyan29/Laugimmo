<?php
// Front controller minimal pour dispatcher vers le contrôleur MVC
require_once __DIR__.'/app/bootstrap.php';

use App\Controllers\ListingsController;

// Route par défaut: page d'accueil des annonces
$controller = new ListingsController();
$controller->index();
?>
