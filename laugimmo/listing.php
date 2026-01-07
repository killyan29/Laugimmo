<?php
// Front controller: délègue au contrôleur MVC
require_once __DIR__.'/app/bootstrap.php';
use App\Controllers\ListingsController;

$controller = new ListingsController();
$controller->show();
?>
