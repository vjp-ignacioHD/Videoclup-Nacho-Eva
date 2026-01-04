<?php


namespace Tests;
use Throwable;

/**
 * Pruebas específicas para CintaVideo
 * 
 * @covers \App\CintaVideo
 */
class CintaVideoTest {
    
    public function testConstructorYPropiedades() {
        $cinta = new \App\CintaVideo("Matrix", 101, 9.99, 136);
        
        assert($cinta->titulo === "Matrix");
        assert($cinta->getNumero() === 101);
        assert($cinta->getPrecio() === 9.99);
        assert($cinta->getAlquilado() === false);
        
        return true;
    }
    
    public function testMuestraResumenContenido() {
        $cinta = new \App\CintaVideo("El Resplandor", 1, 15.50, 120);
        $resumen = $cinta->muestraResumen();
        
        // Verificar elementos clave
        $elementos = [
            "Título: El Resplandor",
            "Número: 1",
            "Precio: 15.50€",
            "Duración: 120 minutos"
        ];
        
        foreach ($elementos as $elemento) {
            assert(str_contains($resumen, $elemento), "Falta: $elemento");
        }
        
        return true;
    }
    
    public function testGetPuntuacionSinUrl() {
        $cinta = new \App\CintaVideo("Sin Metacritic", 999, 10, 90);
        $puntuacion = $cinta->getPuntuacion();
        
        assert($puntuacion === null, "Sin URL debe devolver null");
        return true;
    }
}

// Ejecutar si se llama directamente
if (isset($argv) && basename($argv[0]) == basename(__FILE__)) {
    require_once __DIR__ . '/../autoload.php';
    
    $test = new CintaVideoTest();
    $metodos = get_class_methods($test);
    
    $exitosos = 0;
    $totales = 0;
    
    foreach ($metodos as $metodo) {
        if (strpos($metodo, 'test') === 0) {
            $totales++;
            try {
                if ($test->$metodo()) {
                    echo "✓ $metodo\n";
                    $exitosos++;
                }
            } catch (Throwable $e) {
                echo "✗ $metodo: " . $e->getMessage() . "\n";
            }
        }
    }
    
    echo "\nResultado: $exitosos/$totales\n";
}