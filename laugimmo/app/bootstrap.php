<?php
// Bootstrap simple pour MVC léger
require_once __DIR__.'/../config.php';

// Autoloader PSR-4 minimal pour l'espace de noms App\
spl_autoload_register(function($class){
    $prefix = 'App\\';
    $base_dir = __DIR__.'/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

// Fonction helper pour récupérer PDO via config.php
function app_pdo(): PDO { return get_pdo(); }

?>
