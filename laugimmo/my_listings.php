<?php
// Front controller: délègue au contrôleur MVC pour Mes annonces
require_once __DIR__.'/app/bootstrap.php';
use App\Controllers\ListingsController;

$controller = new ListingsController();
$controller->mine();
?>
