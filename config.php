<?php
// Configuration de la base de données (MAMP par défaut: port 8889, user root, pass root)
$GLOBALS['DB_HOST'] = '127.0.0.1';
$GLOBALS['DB_PORT'] = 8889; // MAMP MySQL
$GLOBALS['DB_NAME'] = 'laugimmo';
$GLOBALS['DB_USER'] = 'root';
$GLOBALS['DB_PASS'] = 'root';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function get_pdo(): PDO {
    $DB_HOST = $GLOBALS['DB_HOST'];
    $DB_PORT = $GLOBALS['DB_PORT'];
    $DB_NAME = $GLOBALS['DB_NAME'];
    $DB_USER = $GLOBALS['DB_USER'];
    $DB_PASS = $GLOBALS['DB_PASS'];
    
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};charset=utf8mb4";
        // if (file_exists('/Applications/MAMP/tmp/mysql/mysql.sock')) {
        //      $dsn .= ";unix_socket=/Applications/MAMP/tmp/mysql/mysql.sock";
        // }
        // echo "DEBUG: Connecting with DSN: $dsn\n"; 
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