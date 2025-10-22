<?php

// Define la raíz del proyecto
define('ROOT', __DIR__);

// Función de autoloading
spl_autoload_register(function ($class) {
    // Eliminamos el namespace base para obtener la ruta relativa
    $prefix = 'Dwes\\ProyectoVideoclub\\';
    
    // Si la clase no empieza con nuestro namespace, no la cargamos
    if (strpos($class, $prefix) !== 0) {
        return;
    }

    // Quitamos el namespace base
    $relative_class = substr($class, strlen($prefix));

    // Convertimos el namespace en ruta de archivo
    $file = ROOT . '/app/' . str_replace('\\', '/', $relative_class) . '.php';

    // Si el archivo existe, lo incluimos
    if (file_exists($file)) {
        require_once $file;
    }
});