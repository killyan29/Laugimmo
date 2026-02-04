<?php
namespace App\Controllers;

class AuthController
{
    public function login(): void
    {
        $pdo = \app_pdo();
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!\verify_csrf($_POST['csrf'] ?? '')) { $errors[] = 'Token CSRF invalide.'; }
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            if (!$errors) {
                $stmt = $pdo->prepare('SELECT id,name,email,password_hash,is_admin FROM users WHERE email = :email');
                $stmt->execute([':email'=>$email]);
                $user = $stmt->fetch();
                if (!$user || !password_verify($password, $user['password_hash'])) {
                    $errors[] = 'Identifiants invalides.';
                } else {
                    $_SESSION['user'] = ['id'=>$user['id'],'name'=>$user['name'],'email'=>$user['email'],'is_admin'=>$user['is_admin']];
                    \redirect('index.php');
                }
            }
        }

        require __DIR__.'/../Views/auth/login.php';
    }

    public function register(): void
    {
        $pdo = \app_pdo();
        $errors = []; $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!\verify_csrf($_POST['csrf'] ?? '')) { $errors[] = 'Token CSRF invalide.'; }
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm = $_POST['confirm'] ?? '';
            if ($name==='') $errors[] = 'Nom requis';
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email invalide';
            if (strlen($password) < 6) $errors[] = 'Mot de passe trop court (≥6)';
            if ($password !== $confirm) $errors[] = 'Les mots de passe ne correspondent pas';

            if (!$errors) {
                $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email');
                $stmt->execute([':email'=>$email]);
                if ($stmt->fetch()) {
                    $errors[] = 'Un compte existe déjà avec cet email.';
                } else {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare('INSERT INTO users (name,email,password_hash,is_admin,created_at) VALUES (:n,:e,:h,0,NOW())');
                    $stmt->execute([':n'=>$name, ':e'=>$email, ':h'=>$hash]);
                    $success = 'Compte créé. Vous pouvez vous connecter.';
                }
            }
        }

        require __DIR__.'/../Views/auth/register.php';
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
        \redirect('index.php');
    }
}

?>
