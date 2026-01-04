<?php
// autoload.php - Versión manual SIN Composer

define('ROOT', __DIR__);

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    
    if (strpos($class, $prefix) !== 0) {
        return;
    }

    $relative_class = substr($class, strlen($prefix));
    $file = ROOT . '/app/' . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});