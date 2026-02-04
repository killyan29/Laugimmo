<?php
require_once __DIR__ . '/../app/bootstrap.php';

use PHPUnit\Framework\TestCase;
use App\Services\ReservationService;

class ReservationServiceTest extends TestCase
{
    private $pdo;
    private $reservationService;

    protected function setUp(): void
    {
        $this->pdo = $this->createMock(PDO::class);
        $this->reservationService = new ReservationService($this->pdo);
    }

    public function testUserMustBeLoggedIn(): void
    {
        $errors = $this->reservationService->create(1, 0, '2023-01-01', '2023-01-05');
        $this->assertContains('Utilisateur non connecté.', $errors);
    }

    public function testListingMustExist(): void
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('fetch')->willReturn(false); // Listing not found

        $this->pdo->method('prepare')->willReturn($stmt);

        $errors = $this->reservationService->create(999, 1, '2023-01-01', '2023-01-05');
        $this->assertContains('Annonce introuvable.', $errors);
    }

    public function testPreventDoubleBooking(): void
    {
        // 1. Mock listing fetch
        $stmtListing = $this->createMock(PDOStatement::class);
        $stmtListing->method('fetch')->willReturn(['price_per_night' => 100]);

        // 2. Mock overlapping check
        $stmtOverlap = $this->createMock(PDOStatement::class);
        $stmtOverlap->method('fetchColumn')->willReturn(1); // 1 overlap found

        $this->pdo->expects($this->exactly(2))
            ->method('prepare')
            ->willReturnOnConsecutiveCalls($stmtListing, $stmtOverlap);

        $errors = $this->reservationService->create(1, 1, '2023-01-01', '2023-01-05');
        $this->assertContains('Ces dates ne sont pas disponibles.', $errors);
    }

    public function testReservationSavedInDatabase(): void
    {
        // 1. Mock listing fetch
        $stmtListing = $this->createMock(PDOStatement::class);
        $stmtListing->method('fetch')->willReturn(['price_per_night' => 100]);

        // 2. Mock overlapping check (0 overlaps)
        $stmtOverlap = $this->createMock(PDOStatement::class);
        $stmtOverlap->method('fetchColumn')->willReturn(0);

        // 3. Mock insert
        $stmtInsert = $this->createMock(PDOStatement::class);
        $stmtInsert->expects($this->once())->method('execute');

        $this->pdo->expects($this->exactly(3))
            ->method('prepare')
            ->willReturnOnConsecutiveCalls($stmtListing, $stmtOverlap, $stmtInsert);

        $errors = $this->reservationService->create(1, 1, '2023-01-01', '2023-01-05');
        $this->assertEmpty($errors);
    }
}
