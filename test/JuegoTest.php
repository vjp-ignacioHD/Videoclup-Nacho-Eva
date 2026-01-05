<?php
namespace Tests;

use Throwable;

/**
 * Pruebas específicas para Juego
 * 
 * @covers \App\Juego
 */
class JuegoTest {
    
    public function testConstructorYPropiedades() {
        $juego = new \App\Juego("The Legend of Zelda", 301, 59.99, "Nintendo Switch", 1, 4);
        
        assert($juego->titulo === "The Legend of Zelda");
        assert($juego->getNumero() === 301);
        assert($juego->getPrecio() === 59.99);
        assert($juego->consola === "Nintendo Switch");
        
        return true;
    }
    
    public function testMuestraJugadoresPosibles() {
        // 1 jugador
        $juego1 = new \App\Juego("Solitario", 1, 10, "PC", 1, 1);
        ob_start();
        $resultado1 = $juego1->muestraJugadoresPosibles();
        $output1 = ob_get_clean();
        
        assert(is_string($resultado1));
        assert(str_contains($resultado1, "Para un jugador"));
        
        // Rango de jugadores
        $juego2 = new \App\Juego("Party Game", 2, 30, "Switch", 2, 8);
        ob_start();
        $resultado2 = $juego2->muestraJugadoresPosibles();
        $output2 = ob_get_clean();
        
        assert(str_contains($resultado2, "De 2 a 8 jugadores"));
        
        return true;
    }
    
    public function testMuestraResumenJugadores() {
        $juego = new \App\Juego("Mario Kart", 4, 39.99, "Switch", 2, 4);
        $resumen = $juego->muestraResumen();
        
        assert(str_contains($resumen, "De 2 a 4 jugadores"));
        return true;
    }
}

if (isset($argv) && basename($argv[0]) == basename(__FILE__)) {
    require_once __DIR__ . '/../autoload.php';
    
    $test = new JuegoTest();
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