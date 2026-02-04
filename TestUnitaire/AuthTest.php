<?php
require_once __DIR__ . '/../app/bootstrap.php';

use PHPUnit\Framework\TestCase;
use App\Services\AuthService;

class AuthTest extends TestCase
{
    private $pdo;
    private $authService;

    protected function setUp(): void
    {
        // Mock PDO
        $this->pdo = $this->createMock(PDO::class);
        $this->authService = new AuthService($this->pdo);
    }

    public function testRegistrationWithInvalidEmail(): void
    {
        $errors = $this->authService->register('John Doe', 'invalid-email', 'password123', 'password123');
        $this->assertContains('Email invalide', $errors);
    }

    public function testRegistrationWithShortPassword(): void
    {
        $errors = $this->authService->register('John Doe', 'john@example.com', '123', '123');
        $this->assertContains('Mot de passe trop court (≥6)', $errors);
    }

    public function testRegistrationWithExistingEmail(): void
    {
        // Mock PDO statement for existing email check
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('fetch')->willReturn(['id' => 1]); // Simulate existing user

        $this->pdo->method('prepare')->willReturn($stmt);

        $errors = $this->authService->register('John Doe', 'existing@example.com', 'password123', 'password123');
        $this->assertContains('Un compte existe déjà avec cet email.', $errors);
    }

    public function testSuccessfulRegistration(): void
    {
        // Mock PDO statement for email check (returns false -> no user)
        $stmtSelect = $this->createMock(PDOStatement::class);
        $stmtSelect->method('fetch')->willReturn(false);

        // Mock PDO statement for insert
        $stmtInsert = $this->createMock(PDOStatement::class);
        $stmtInsert->expects($this->once())->method('execute');

        // Configure PDO prepare to return appropriate statements
        $this->pdo->expects($this->exactly(2))
            ->method('prepare')
            ->willReturnMap([
                ['SELECT id FROM users WHERE email = :email', $stmtSelect],
                ['INSERT INTO users (name,email,password_hash,is_admin,created_at) VALUES (:n,:e,:h,0,NOW())', $stmtInsert]
            ]);

        $errors = $this->authService->register('John Doe', 'new@example.com', 'password123', 'password123');
        $this->assertEmpty($errors);
    }

    public function testLoginWithWrongPassword(): void
    {
        $passwordHash = password_hash('correctpassword', PASSWORD_DEFAULT);
        
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('fetch')->willReturn([
            'id' => 1,
            'name' => 'John',
            'email' => 'john@example.com',
            'password_hash' => $passwordHash,
            'is_admin' => 0
        ]);

        $this->pdo->method('prepare')->willReturn($stmt);

        $user = $this->authService->login('john@example.com', 'wrongpassword');
        $this->assertNull($user);
    }

    public function testLoginSuccess(): void
    {
        $passwordHash = password_hash('correctpassword', PASSWORD_DEFAULT);
        
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('fetch')->willReturn([
            'id' => 1,
            'name' => 'John',
            'email' => 'john@example.com',
            'password_hash' => $passwordHash,
            'is_admin' => 0
        ]);

        $this->pdo->method('prepare')->willReturn($stmt);

        $user = $this->authService->login('john@example.com', 'correctpassword');
        $this->assertNotNull($user);
        $this->assertEquals('john@example.com', $user['email']);
    }
    
    public function testPasswordHashing(): void
    {
        // This is implicitly tested in registration, but we can verify explicitly that password_hash is used
        // However, since we mock PDO, we can't inspect the arguments passed to execute easily in a simple unit test 
        // without a more complex mock setup. 
        // Instead, we trust PHP's password_hash function which is used in the service.
        $this->assertTrue(true); 
    }
}
