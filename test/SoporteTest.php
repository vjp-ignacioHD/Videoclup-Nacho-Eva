<?php
namespace Tests;

use Throwable;

/**
 * Pruebas para la clase abstracta Soporte
 * 
 * @covers \App\Soporte
 * @covers \App\CintaVideo
 * @covers \App\Dvd
 * @covers \App\Juego
 */
class SoporteTest {
    
    public function testCintaVideoMuestraResumenDevuelveString() {
        $cinta = new \App\CintaVideo("El resplandor", 1, 5.99, 120);
        $resultado = $cinta->muestraResumen();
        
        // Verificar que devuelve string
        assert(is_string($resultado), "CintaVideo::muestraResumen() debe devolver string");
        
        // Verificar contenido
        assert(str_contains($resultado, "Título: El resplandor"));
        assert(str_contains($resultado, "Número: 1"));
        assert(str_contains($resultado, "Precio: 5.99€"));
        assert(str_contains($resultado, "Duración: 120 minutos"));
        
        return true;
    }
    
    public function testDvdMuestraResumenDevuelveString() {
        $dvd = new \App\Dvd("El Padrino", 2, 9.99, "ES,EN,FR", "16:9");
        $resultado = $dvd->muestraResumen();
        
        assert(is_string($resultado), "Dvd::muestraResumen() debe devolver string");
        assert(str_contains($resultado, "Idiomas: ES,EN,FR"));
        assert(str_contains($resultado, "Formato de pantalla: 16:9"));
        
        return true;
    }
    
    public function testJuegoMuestraResumenDevuelveString() {
        $juego = new \App\Juego("The Legend of Zelda", 3, 49.99, "Nintendo Switch", 1, 1);
        $resultado = $juego->muestraResumen();
        
        assert(is_string($resultado), "Juego::muestraResumen() debe devolver string");
        assert(str_contains($resultado, "Consola: Nintendo Switch"));
        assert(str_contains($resultado, "Para un jugador"));
        
        return true;
    }
    
    public function testJuegoMultipleJugadores() {
        $juego = new \App\Juego("Mario Kart", 4, 39.99, "Nintendo Switch", 2, 4);
        $resultado = $juego->muestraResumen();
        
        assert(str_contains($resultado, "De 2 a 4 jugadores"));
        return true;
    }
    
    public function testPrecioConIva() {
        $cinta = new \App\CintaVideo("Test", 5, 100, 90);
        $precioConIva = $cinta->getPrecioConIva();
        
        assert($precioConIva == 121, "Precio con IVA debe ser 121");
        return true;
    }
    
    public function testEstadoAlquilado() {
        $dvd = new \App\Dvd("Test", 6, 10, "ES", "4:3");
        
        assert($dvd->getAlquilado() === false, "Inicialmente no alquilado");
        
        $dvd->setAlquilado(true);
        assert($dvd->getAlquilado() === true, "Debe estar alquilado");
        
        $dvd->setAlquilado(false);
        assert($dvd->getAlquilado() === false, "Debe estar devuelto");
        
        return true;
    }
}

// Ejecutar pruebas si se llama directamente
if (isset($argv) && basename($argv[0]) == basename(__FILE__)) {
    require_once __DIR__ . '/../autoload.php';
    
    $test = new SoporteTest();
    $metodos = get_class_methods($test);
    
    $exitosos = 0;
    $totales = 0;
    
    foreach ($metodos as $metodo) {
        if (strpos($metodo, 'test') === 0) {
            $totales++;
            try {
                $resultado = $test->$metodo();
                if ($resultado === true) {
                    echo "✓ $metodo: PASÓ\n";
                    $exitosos++;
                } else {
                    echo "✗ $metodo: FALLÓ (devuelve falso)\n";
                }
            } catch (Throwable $e) {
                echo "✗ $metodo: ERROR - " . $e->getMessage() . "\n";
            }
        }
    }
    
    echo "\nRESULTADO: $exitosos/$totales pruebas exitosas\n";
    exit($exitosos === $totales ? 0 : 1);
}