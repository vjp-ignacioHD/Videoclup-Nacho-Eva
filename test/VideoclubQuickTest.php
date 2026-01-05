<?php
// Test rápido e independiente - NO usa bootstrap.php

// Incluir directamente el autoload de vendor
require_once __DIR__ . '/../vendor/autoload.php';

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
    
    // Test 2: Constructor
    $total++;
    $videoclub = new App\Videoclub('Mi Videoclub');
    if ($videoclub->getNombre() === 'Mi Videoclub') {
        $tests[] = "✓ Constructor funciona";
        $passed++;
    } else {
        $tests[] = "✗ Constructor falló";
    }

    // Test 3: Incluir socio
    $total++;
    $videoclub->incluirSocio('Juan Pérez');
    if ($videoclub->getNumSocios() === 1) {
        $tests[] = "✓ Incluir socio funciona";
        $passed++;
    } else {
        $tests[] = "✗ Incluir socio falló";
    }

    // Test 4: Incluir DVD
    $total++;
    $videoclub->incluirDvd('El Padrino', 14.99, 'Español, Inglés', '16:9');
    if ($videoclub->getNumProductos() === 1) {
        $tests[] = "✓ Incluir DVD funciona";
        $passed++;
    } else {
        $tests[] = "✗ Incluir DVD falló";
    }

    // Test 5: Alquilar
    $total++;
    $videoclub->alquilaSocioProducto(1, 0);
    if ($videoclub->getNumProductosAlquilados() === 1) {
        $tests[] = "✓ Alquilar producto funciona";
        $passed++;
    } else {
        $tests[] = "✗ Alquilar producto falló";
    }

    // Test 6: Excepción socio no existe
    $total++;
    try {
        $videoclub->alquilaSocioProducto(999, 0);
        $tests[] = "✗ No lanzó excepción para socio no existe";
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'Socio 999') !== false || 
            strpos($e->getMessage(), 'no encontrado') !== false) {
            $tests[] = "✓ Excepción socio no existe funciona";
            $passed++;
        } else {
            $tests[] = "✗ Excepción diferente: " . $e->getMessage();
        }
    }

    // Test 7: Excepción producto no existe
    $total++;
    try {
        $videoclub->alquilaSocioProducto(1, 999);
        $tests[] = "✗ No lanzó excepción para producto no existe";
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'Soporte 999') !== false || 
            strpos($e->getMessage(), 'no encontrado') !== false) {
            $tests[] = "✓ Excepción producto no existe funciona";
            $passed++;
        } else {
            $tests[] = "✗ Excepción diferente: " . $e->getMessage();
        }
    }

    // Test 8: Excepción producto ya alquilado
    $total++;
    $videoclub->incluirSocio('María');
    try {
        $videoclub->alquilaSocioProducto(2, 0);
        $tests[] = "✗ No lanzó excepción para producto ya alquilado";
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'ya está alquilado') !== false || 
            strpos($e->getMessage(), 'ya tiene alquilado') !== false) {
            $tests[] = "✓ Excepción producto ya alquilado funciona";
            $passed++;
        } else {
            $tests[] = "✗ Excepción diferente: " . $e->getMessage();
        }
    }

    // Test 9: Excepción cupo superado
    $total++;
    $videoclub->incluirSocio('Pedro', null, 1); // Cupo 1
    $videoclub->incluirDvd('Película 2', 10, 'ES', '16:9', 100);
    $videoclub->incluirDvd('Película 3', 10, 'ES', '16:9', 101);
    
    $videoclub->alquilaSocioProducto(3, 100);
    try {
        $videoclub->alquilaSocioProducto(3, 101);
        $tests[] = "✗ No lanzó excepción para cupo superado";
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'Cupo') !== false || 
            strpos($e->getMessage(), 'cupo') !== false ||
            strpos($e->getMessage(), 'límite') !== false) {
            $tests[] = "✓ Excepción cupo superado funciona";
            $passed++;
        } else {
            $tests[] = "✗ Excepción diferente: " . $e->getMessage();
        }
    }

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