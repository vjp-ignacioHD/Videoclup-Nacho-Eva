<?php
// Test rápido e independiente - NO usa bootstrap.php

// Incluir directamente el autoload de vendor
require_once __DIR__ . '/../vendor/autoload.php';

use App\Videoclub;
use Dwes\Videoclub\Exception\ClienteNoExisteException;  // Cambiado
use Dwes\Videoclub\Exception\SoporteNoEncontradoException;  // Cambiado
use Dwes\Videoclub\Exception\SoporteYaAlquiladoException;  // Cambiado
use Dwes\Videoclub\Exception\CupoSuperadoException;  // Cambiado

echo "=== TEST RÁPIDO DEL VIDEOCLUB ===\n\n";

$tests = [];
$passed = 0;
$total = 0;

try {
    // Test 1: Verificar que existe la clase Videoclub
    $total++;
    if (class_exists('App\Videoclub')) {
        $tests[] = "✓ Clase Videoclub existe";
        $passed++;
    } else {
        $tests[] = "✗ Clase Videoclub NO existe";
        throw new Exception("La clase Videoclub no está definida");
    }
    
    // Test 6: Excepción socio no existe (nueva excepción)
    $total++;
    $videoclub = new Videoclub('Test');
    $videoclub->incluirDvd('Película', 10, 'ES', '16:9');
    
    try {
        $videoclub->alquilaSocioProducto(999, 0);
        $tests[] = "✗ No lanzó excepción para socio no existe";
    } catch (ClienteNoExisteException $e) {  // Cambiado
        $tests[] = "✓ ClienteNoExisteException funciona";
        $passed++;
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'Socio 999') !== false || 
            strpos($e->getMessage(), 'no encontrado') !== false) {
            $tests[] = "✓ Excepción socio no existe (con mensaje correcto)";
            $passed++;
        } else {
            $tests[] = "✗ Excepción diferente: " . $e->getMessage();
        }
    }

    // Resto de tests continúan...
    // (Actualizar las otras excepciones también)
} catch (Exception $e) {
    $tests[] = "✗ Error general: " . $e->getMessage();
    $tests[] = "Trace: " . $e->getTraceAsString();
}

// Mostrar resultados
echo "Resultados:\n";
foreach ($tests as $test) {
    echo $test . "\n";
}

echo "\nResumen: $passed/$total tests pasaron\n";
if ($passed === $total) {
    echo "✅ ¡TODOS LOS TESTS PASARON!\n";
} else {
    echo "⚠️  Algunos tests fallaron\n";
    exit(1);
}