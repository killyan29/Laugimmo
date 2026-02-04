<?php
// Front controller: délègue au contrôleur MVC pour inscription
require_once __DIR__.'/app/bootstrap.php';
use App\Controllers\AuthController;

$controller = new AuthController();
$controller->register();
?>
