<?php
namespace Tests;

use Throwable;

/**
 * Pruebas específicas para Bluray
 * 
 * @covers \App\Bluray
 */
class BlurayTest {
    
    public function testConstructorYPropiedades() {
        $bluray = new \App\Bluray("Avatar", 301, 24.99, 162, true);
        
        assert($bluray->titulo === "Avatar");
        assert($bluray->getNumero() === 301);
        assert($bluray->getPrecio() === 24.99);
        assert($bluray->getDuracion() === 162);
        assert($bluray->es4k() === true);
        
        return true;
    }
    
    public function testMuestraResumenContenido() {
        $bluray = new \App\Bluray("Pelicula Test", 1, 29.99, 120, false);
        
        ob_start();
        $resumen = $bluray->muestraResumen();
        ob_end_clean();
        
        assert(str_contains($resumen, "<strong>Duración:</strong> 120 minutos"));
        assert(str_contains($resumen, "<strong>4K:</strong> No"));
        
        return true;
    }
    
    public function testMuestraResumen4K() {
        $bluray = new \App\Bluray("Pelicula 4K", 2, 34.99, 150, true);
        
        ob_start();
        $resumen = $bluray->muestraResumen();
        ob_end_clean();
        
        assert(str_contains($resumen, "<strong>4K:</strong> Sí"));
        
        return true;
    }
    
    public function testGetterDuracion() {
        $bluray = new \App\Bluray("Test", 1, 10, 90, true);
        assert($bluray->getDuracion() === 90);
        return true;
    }
    
    public function testEs4k() {
        $bluray4k = new \App\Bluray("4K Movie", 1, 20, 120, true);
        $blurayNormal = new \App\Bluray("Normal Movie", 2, 15, 100, false);
        
        assert($bluray4k->es4k() === true);
        assert($blurayNormal->es4k() === false);
        
        return true;
    }
}

if (isset($argv) && basename($argv[0]) == basename(__FILE__)) {
    require_once __DIR__ . '/../autoload.php';
    
    $test = new BlurayTest();
    $metodos = get_class_methods($test);
    
    foreach ($metodos as $metodo) {
        if (strpos($metodo, 'test') === 0) {
            try {
                if ($test->$metodo()) {
                    echo "✓ $metodo\n";
                }
            } catch (Throwable $e) {
                echo "✗ $metodo: " . $e->getMessage() . "\n";
            }
        }
    }
}