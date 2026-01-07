<?php
// Configuration de la base de données (MAMP par défaut: port 8889, user root, pass root)
$DB_HOST = 'localhost';
$DB_PORT = 8889; // MAMP MySQL
$DB_NAME = 'laugimmo';
$DB_USER = 'root';
$DB_PASS = 'root';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function get_pdo(): PDO {
    global $DB_HOST, $DB_PORT, $DB_NAME, $DB_USER, $DB_PASS;
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};charset=utf8mb4";
        $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
    return $pdo;
}

function redirect(string $url): void {
    header("Location: $url");
    exit;
}

function is_logged_in(): bool {
    return isset($_SESSION['user']);
}

function current_user(): ?array {
    return $_SESSION['user'] ?? null;
}

function require_login(): void {
    if (!is_logged_in()) {
        redirect('login.php');
    }
}

function is_admin(): bool {
    return is_logged_in() && !empty($_SESSION['user']['is_admin']);
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(string $token): bool {
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

function h(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

?>