<?php
namespace App\Services;

use PDO;

class AuthService
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function register(string $name, string $email, string $password, string $confirm): array
    {
        $errors = [];
        if ($name === '') $errors[] = 'Nom requis';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email invalide';
        if (strlen($password) < 6) $errors[] = 'Mot de passe trop court (≥6)';
        if ($password !== $confirm) $errors[] = 'Les mots de passe ne correspondent pas';

        if (!$errors) {
            $stmt = $this->pdo->prepare('SELECT id FROM users WHERE email = :email');
            $stmt->execute([':email' => $email]);
            if ($stmt->fetch()) {
                $errors[] = 'Un compte existe déjà avec cet email.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $this->pdo->prepare('INSERT INTO users (name,email,password_hash,is_admin,created_at) VALUES (:n,:e,:h,0,NOW())');
                $stmt->execute([':n' => $name, ':e' => $email, ':h' => $hash]);
            }
        }
        return $errors;
    }

    public function login(string $email, string $password): ?array
    {
        $stmt = $this->pdo->prepare('SELECT id,name,email,password_hash,is_admin FROM users WHERE email = :email');
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();
        if (!$user || !password_verify($password, $user['password_hash'])) {
            return null;
        }
        return $user;
    }
}
