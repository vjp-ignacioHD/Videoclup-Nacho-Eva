<?php

namespace Tests;

use Throwable;

/**
 * Pruebas específicas para Dvd
 * 
 * @covers \App\Dvd
 */
class DvdTest
{

    public function testConstructorYPropiedades()
    {
        $dvd = new \App\Dvd("El Padrino", 201, 19.99, "ES,EN,FR", "16:9", 175);

        assert($dvd->titulo === "El Padrino");
        assert($dvd->getNumero() === 201);
        assert($dvd->getPrecio() === 19.99);
        assert($dvd->idiomas === "ES,EN,FR");
        assert($dvd->getDuracion() === 175); // Nueva verificación

        return true;
    }

    public function testMuestraResumenContenido()
    {
        $dvd = new \App\Dvd("Pelicula Test", 1, 29.99, "ES,EN", "4:3", 120);

        ob_start();
        $resumen = $dvd->muestraResumen();
        ob_end_clean();

        // Buscar el patrón correcto con la etiqueta HTML
        assert(str_contains($resumen, "<strong>Idiomas:</strong> ES,EN"));
        assert(str_contains($resumen, "<strong>Formato de pantalla:</strong> 4:3"));
        assert(str_contains($resumen, "<strong>Duración:</strong> 120 minutos"));

        return true;
    }

    public function testGetterDuracion()
    {
        $dvd = new \App\Dvd("Test", 1, 10, "ES", "16:9", 90);
        assert($dvd->getDuracion() === 90);
        return true;
    }
}

if (isset($argv) && basename($argv[0]) == basename(__FILE__)) {
    require_once __DIR__ . '/../autoload.php';

    $test = new DvdTest();
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
