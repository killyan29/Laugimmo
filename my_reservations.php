<?php
// Front controller: délègue au contrôleur MVC pour Mes réservations
require_once __DIR__.'/app/bootstrap.php';
use App\Controllers\ReservationsController;

$controller = new ReservationsController();
$controller->mine();
?>
