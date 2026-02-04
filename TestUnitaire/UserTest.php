<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/bootstrap.php';
require "./app/Controllers/AuthController.php";

use PHPUnit\Framework\TestCase;
use App\Models\Listing;

final class UserTest extends TestCase {

public function testFindNonExistingUser(): void {

    $id = 999999;

    $this->assertNull("$user $id ne devrait pas exister");
}

public function testFindExistingUser(): void {

    $id = 1;

    $this->assertNotNull("$user $id devrait exister");
}



}