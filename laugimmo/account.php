<?php
// Front controller: délègue à AccountController@index
require_once __DIR__.'/app/bootstrap.php';
use App\Controllers\AccountController;

$controller = new AccountController();
$controller->index();
?>
