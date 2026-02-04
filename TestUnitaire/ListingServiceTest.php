<?php
require_once __DIR__ . '/../app/bootstrap.php';

use PHPUnit\Framework\TestCase;
use App\Services\ListingService;

class ListingServiceTest extends TestCase
{
    private $pdo;
    private $listingService;

    protected function setUp(): void
    {
        $this->pdo = $this->createMock(PDO::class);
        $this->listingService = new ListingService($this->pdo);
    }

    public function testRequiredFields(): void
    {
        $data = [
            'title' => '', // Empty title
            'price' => 100,
            'rooms' => 2,
            'location' => 'Paris'
        ];

        $errors = $this->listingService->create(1, $data);
        $this->assertContains('Titre requis', $errors);
    }

    public function testPositivePrice(): void
    {
        $data = [
            'title' => 'Villa',
            'price' => -50, // Negative price
            'rooms' => 2,
            'location' => 'Paris'
        ];

        $errors = $this->listingService->create(1, $data);
        $this->assertContains('Prix incorrect', $errors);
    }

    public function testValidRooms(): void
    {
        $data = [
            'title' => 'Villa',
            'price' => 100,
            'rooms' => 0, // Invalid rooms
            'location' => 'Paris'
        ];

        $errors = $this->listingService->create(1, $data);
        $this->assertContains('Nombre de pièces incorrect', $errors);
    }

    public function testInvalidImage(): void
    {
        $data = [
            'title' => 'Villa',
            'price' => 100,
            'rooms' => 2,
            'location' => 'Paris'
        ];
        
        // Mock file upload array
        $files = [
            'name' => ['test.exe'],
            'type' => ['application/x-msdownload'],
            'tmp_name' => ['/tmp/php123'],
            'error' => [0],
            'size' => [123]
        ];

        $errors = $this->listingService->create(1, $data, $files);
        $this->assertContains('Type de fichier non autorisé pour l\'image 1', $errors);
    }

    public function testListingSavedInDatabase(): void
    {
        $data = [
            'title' => 'Super Villa',
            'description' => 'Vue mer',
            'price' => 200,
            'rooms' => 4,
            'location' => 'Nice',
            'category' => 'maison',
            'has_pool' => 1
        ];

        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())->method('execute');

        $this->pdo->method('prepare')->willReturn($stmt);
        $this->pdo->method('lastInsertId')->willReturn('123');

        $errors = $this->listingService->create(1, $data);
        $this->assertEmpty($errors);
    }
}
