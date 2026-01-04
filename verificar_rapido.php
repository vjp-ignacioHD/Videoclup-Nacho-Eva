<?php
// verificar_super_rapido.php - NO requiere vendor/autoload.php

// Incluir manualmente las clases
require_once 'app/Resumible.php';
require_once 'app/Soporte.php';
require_once 'app/CintaVideo.php';
require_once 'app/Dvd.php';
require_once 'app/Juego.php';

use App\CintaVideo;
use App\Dvd;
use App\Juego;

echo "=== VERIFICACIÓN SUPER RÁPIDA ===\n\n";

// Test 1: Verificar que las clases existen
echo "1. Clases cargadas:\n";
echo "   - Resumible: " . (interface_exists('App\Resumible') ? "✓" : "✗") . "\n";
echo "   - Soporte: " . (class_exists('App\Soporte') ? "✓" : "✗") . "\n";
echo "   - CintaVideo: " . (class_exists('App\CintaVideo') ? "✓" : "✗") . "\n";
echo "   - Dvd: " . (class_exists('App\Dvd') ? "✓" : "✗") . "\n";
echo "   - Juego: " . (class_exists('App\Juego') ? "✓" : "✗") . "\n";

// Test 2: Instanciar y probar
echo "\n2. Instanciación y métodos:\n";

try {
    $cinta = new CintaVideo("Matrix", 1, 9.99, 136);
    echo "   - CintaVideo instanciada: ✓\n";
    
    $result = $cinta->muestraResumen();
    echo "   - muestraResumen() devuelve string: " . (is_string($result) ? "✓" : "✗") . "\n";
    
    $dvd = new Dvd("El Padrino", 2, 14.99, "ES,EN", "16:9");
    echo "   - Dvd instanciado: ✓\n";
    
    $result = $dvd->muestraResumen();
    echo "   - muestraResumen() devuelve string: " . (is_string($result) ? "✓" : "✗") . "\n";
    
    $juego = new Juego("Zelda", 3, 59.99, "Switch", 1, 1);
    echo "   - Juego instanciado: ✓\n";
    
    $result = $juego->muestraResumen();
    echo "   - muestraResumen() devuelve string: " . (is_string($result) ? "✓" : "✗") . "\n";
    
} catch (Exception $e) {
    echo "   ✗ ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== Si ves muchos ✓, todo está BIEN ===\n";