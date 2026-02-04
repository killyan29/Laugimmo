<?php
// Front controller: délègue au contrôleur MVC pour création
require_once __DIR__.'/app/bootstrap.php';
use App\Controllers\ListingsController;

$controller = new ListingsController();
$controller->create();
?>
