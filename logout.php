<?php
// Front controller: délègue au contrôleur MVC pour déconnexion
require_once __DIR__.'/app/bootstrap.php';
use App\Controllers\AuthController;

$controller = new AuthController();
$controller->logout();
