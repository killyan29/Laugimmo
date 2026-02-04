<?php
// Front controller: délègue au contrôleur MVC pour messagerie
require_once __DIR__.'/app/bootstrap.php';
use App\Controllers\MessagesController;

$controller = new MessagesController();
$controller->index();
?>
