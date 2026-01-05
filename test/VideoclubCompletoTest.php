<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Videoclub;
use App\Util\ClienteNoEncontradoException;
use App\Util\SoporteNoEncontradoException;
use App\Util\SoporteYaAlquiladoException;
use App\Util\CupoSuperadoException;

class VideoclubCompletoTest extends TestCase
{
    private Videoclub $videoclub;

    protected function setUp(): void
    {
        $this->videoclub = new Videoclub('VideoClub Premier');
    }

    // ==================== PRUEBAS DEL CONSTRUCTOR ====================
    
    public function testConstructor()
    {
        $this->assertSame('VideoClub Premier', $this->videoclub->getNombre());
        $this->assertSame(0, $this->videoclub->getNumProductos());
        $this->assertSame(0, $this->videoclub->getNumSocios());
        $this->assertSame(0, $this->videoclub->getNumProductosAlquilados());
        $this->assertSame(0, $this->videoclub->getNumTotalAlquileres());
    }

    // ==================== PRUEBAS DE INCLUSIÓN ====================
    
    public function testIncluirSocio()
    {
        $this->videoclub->incluirSocio('Juan Pérez');
        $this->videoclub->incluirSocio('María García', 100);
        
        $this->assertSame(2, $this->videoclub->getNumSocios());
    }

    public function testIncluirSocioConCupoPersonalizado()
    {
        $this->videoclub->incluirSocio('Cliente VIP', null, 5);
        $this->videoclub->incluirSocio('Cliente Básico', null, 2);
        
        $this->assertSame(2, $this->videoclub->getNumSocios());
    }

    public function testIncluirDvd()
    {
        $this->videoclub->incluirDvd('El Padrino', 14.99, 'Español, Inglés', '16:9');
        $this->videoclub->incluirDvd('Interestelar', 19.99, 'Español', 'IMAX', 50);
        
        $this->assertSame(2, $this->videoclub->getNumProductos());
    }

    public function testIncluirJuego()
    {
        $this->videoclub->incluirJuego('The Legend of Zelda', 59.99, 'Nintendo Switch', 1, 1);
        $this->videoclub->incluirJuego('FIFA 23', 69.99, 'PS5', 1, 4, 200);
        
        $this->assertSame(2, $this->videoclub->getNumProductos());
    }

    public function testIncluirCintaVideo()
    {
        $this->videoclub->incluirCintaVideo('Titanic', 9.99, 194);
        $this->videoclub->incluirCintaVideo('Jurassic Park', 7.99, 127, 300);
        
        $this->assertSame(2, $this->videoclub->getNumProductos());
    }

    // ==================== PRUEBAS DE BÚSQUEDA ====================
    
    public function testGetSocioExiste()
    {
        $this->videoclub->incluirSocio('Juan Pérez', 10);
        
        $socio = $this->videoclub->getSocio(10);
        $this->assertNotNull($socio);
        $this->assertSame('Juan Pérez', $socio['nombre']);
        $this->assertSame(0, $socio['alquilados']);
    }

    public function testGetSocioNoExiste()
    {
        $socio = $this->videoclub->getSocio(999);
        $this->assertNull($socio);
    }

    public function testGetProductoExiste()
    {
        $this->videoclub->incluirDvd('El Padrino', 14.99, 'Español', '16:9', 20);
        
        $producto = $this->videoclub->getProducto(20);
        $this->assertNotNull($producto);
        $this->assertSame('El Padrino', $producto['titulo']);
        $this->assertSame('dvd', $producto['tipo']);
        $this->assertNull($producto['alquiladoBy']);
    }

    public function testGetProductoNoExiste()
    {
        $producto = $this->videoclub->getProducto(999);
        $this->assertNull($producto);
    }

    // ==================== PRUEBAS DE ALQUILER INDIVIDUAL ====================
    
    public function testAlquilaSocioProductoExitoso()
    {
        $this->videoclub->incluirSocio('Juan Pérez');
        $this->videoclub->incluirDvd('El Padrino', 14.99, 'Español', '16:9');
        
        $this->videoclub->alquilaSocioProducto(1, 0);
        
        $this->assertSame(1, $this->videoclub->getNumProductosAlquilados());
        $this->assertSame(1, $this->videoclub->getNumTotalAlquileres());
        
        $producto = $this->videoclub->getProducto(0);
        $this->assertSame(1, $producto['alquiladoBy']);
        
        $socio = $this->videoclub->getSocio(1);
        $this->assertSame(1, $socio['alquilados']);
    }

    public function testAlquilaSocioProductoLanzaExcepcionSiSocioNoExiste()
    {
        $this->videoclub->incluirDvd('El Padrino', 14.99, 'Español', '16:9');
        
        $this->expectException(ClienteNoEncontradoException::class);
        $this->expectExceptionMessage('Socio 999 no encontrado');
        
        $this->videoclub->alquilaSocioProducto(999, 0);
    }

    public function testAlquilaSocioProductoLanzaExcepcionSiProductoNoExiste()
    {
        $this->videoclub->incluirSocio('Juan Pérez');
        
        $this->expectException(SoporteNoEncontradoException::class);
        $this->expectExceptionMessage('Soporte 999 no encontrado');
        
        $this->videoclub->alquilaSocioProducto(1, 999);
    }

    public function testAlquilaSocioProductoLanzaExcepcionSiProductoYaAlquilado()
    {
        $this->videoclub->incluirSocio('Juan Pérez');
        $this->videoclub->incluirSocio('María García');
        $this->videoclub->incluirDvd('El Padrino', 14.99, 'Español', '16:9');

        $this->videoclub->alquilaSocioProducto(1, 0);
        
        $this->expectException(SoporteYaAlquiladoException::class);
        $this->expectExceptionMessage('El soporte 0 ya está alquilado');
        
        $this->videoclub->alquilaSocioProducto(2, 0);
    }

    public function testAlquilaSocioProductoLanzaExcepcionSiSuperaCupo()
    {
        $this->videoclub->incluirSocio('Juan Pérez', null, 1); // Cupo de 1
        $this->videoclub->incluirDvd('Película 1', 10, 'ES', '16:9');
        $this->videoclub->incluirDvd('Película 2', 10, 'ES', '16:9');

        $this->videoclub->alquilaSocioProducto(1, 0);
        
        $this->expectException(CupoSuperadoException::class);
        $this->expectExceptionMessageMatches('/elementos alquilados/');
        
        $this->videoclub->alquilaSocioProducto(1, 1);
    }

    // ==================== PRUEBAS DE ALQUILER MÚLTIPLE ====================
    
    public function testAlquilarSocioProductosExitoso()
    {
        $this->videoclub->incluirSocio('Juan Pérez', null, 3);
        
        // Crear 3 productos
        for ($i = 0; $i < 3; $i++) {
            $this->videoclub->incluirDvd("Película $i", 10, 'ES', '16:9');
        }
        
        $this->videoclub->alquilarSocioProductos(1, [0, 1, 2]);
        
        $this->assertSame(3, $this->videoclub->getNumProductosAlquilados());
        $this->assertSame(3, $this->videoclub->getNumTotalAlquileres());
        
        $socio = $this->videoclub->getSocio(1);
        $this->assertSame(3, $socio['alquilados']);
        
        // Verificar que todos están alquilados por el socio 1
        for ($i = 0; $i < 3; $i++) {
            $producto = $this->videoclub->getProducto($i);
            $this->assertSame(1, $producto['alquiladoBy']);
        }
    }

    public function testAlquilarSocioProductosConIdsNoConsecutivos()
    {
        $this->videoclub->incluirSocio('Juan Pérez', null, 3);
        
        // Crear productos con IDs específicos
        $this->videoclub->incluirDvd('Película A', 10, 'ES', '16:9', 10);
        $this->videoclub->incluirDvd('Película B', 10, 'ES', '16:9', 20);
        $this->videoclub->incluirDvd('Película C', 10, 'ES', '16:9', 30);
        
        $this->videoclub->alquilarSocioProductos(1, [10, 20, 30]);
        
        $this->assertSame(3, $this->videoclub->getNumProductosAlquilados());
        
        // Verificar cada producto
        foreach ([10, 20, 30] as $id) {
            $producto = $this->videoclub->getProducto($id);
            $this->assertSame(1, $producto['alquiladoBy']);
        }
    }

    public function testAlquilarSocioProductosLanzaExcepcionSiSocioNoExiste()
    {
        $this->videoclub->incluirDvd('Película', 10, 'ES', '16:9');
        
        $this->expectException(ClienteNoEncontradoException::class);
        
        $this->videoclub->alquilarSocioProductos(999, [0]);
    }

    public function testAlquilarSocioProductosLanzaExcepcionSiProductoNoExiste()
    {
        $this->videoclub->incluirSocio('Juan Pérez');
        
        $this->expectException(SoporteNoEncontradoException::class);
        
        $this->videoclub->alquilarSocioProductos(1, [0, 999]);
    }

    public function testAlquilarSocioProductosLanzaExcepcionSiAlgunoYaAlquilado()
    {
        $this->videoclub->incluirSocio('Juan Pérez', null, 3);
        
        // Crear 3 productos
        for ($i = 0; $i < 3; $i++) {
            $this->videoclub->incluirDvd("Película $i", 10, 'ES', '16:9');
        }
        
        // Alquilar uno individualmente primero - NO hacer esto
        // En su lugar, crear un escenario diferente
        
        // Cambiar el test: socio con cupo 2, intenta alquilar 2 productos
        // pero uno ya está alquilado por otro socio
        $this->videoclub->incluirSocio('María', null, 2);
        $this->videoclub->alquilaSocioProducto(2, 1); // María alquila producto 1
        
        // Ahora Juan intenta alquilar [0, 1, 2] - el 1 ya está alquilado
        $this->expectException(SoporteYaAlquiladoException::class);
        
        $this->videoclub->alquilarSocioProductos(1, [0, 1, 2]);
    }

    public function testAlquilarSocioProductosLanzaExcepcionSiSuperaCupo()
    {
        $this->videoclub->incluirSocio('Juan Pérez', null, 2); // Cupo 2
        
        // Crear 3 productos
        for ($i = 0; $i < 3; $i++) {
            $this->videoclub->incluirDvd("Película $i", 10, 'ES', '16:9');
        }
        
        $this->expectException(CupoSuperadoException::class);
        
        $this->videoclub->alquilarSocioProductos(1, [0, 1, 2]);
    }

    public function testAlquilarSocioProductosTodoONada()
    {
        $this->videoclub->incluirSocio('Juan Pérez', null, 4); // Cupo 4 para evitar error de cupo
        
        // Crear 3 productos
        for ($i = 0; $i < 3; $i++) {
            $this->videoclub->incluirDvd("Película $i", 10, 'ES', '16:9');
        }
        
        // Alquilar el producto 1 individualmente primero
        $this->videoclub->alquilaSocioProducto(1, 1);
        
        try {
            // Intentar alquilar [0, 1, 2] - debería fallar porque 1 ya está alquilado
            $this->videoclub->alquilarSocioProductos(1, [0, 1, 2]);
            $this->fail('Debería haber lanzado excepción');
        } catch (SoporteYaAlquiladoException $e) {
            // Verificar que NINGUNO se alquiló (todo o nada)
            $producto0 = $this->videoclub->getProducto(0);
            $producto2 = $this->videoclub->getProducto(2);
            
            $this->assertNull($producto0['alquiladoBy'], 'Producto 0 no debería estar alquilado');
            $this->assertNull($producto2['alquiladoBy'], 'Producto 2 no debería estar alquilado');
            
            // El producto 1 sigue alquilado (ya lo estaba)
            $producto1 = $this->videoclub->getProducto(1);
            $this->assertSame(1, $producto1['alquiladoBy']);
        }
    }

    // ==================== PRUEBAS DE DEVOLUCIÓN INDIVIDUAL ====================
    
    public function testDevolverSocioProductoExitoso()
    {
        $this->videoclub->incluirSocio('Juan Pérez');
        $this->videoclub->incluirDvd('El Padrino', 14.99, 'Español', '16:9');
        
        $this->videoclub->alquilaSocioProducto(1, 0);
        $this->assertSame(1, $this->videoclub->getNumProductosAlquilados());
        
        $this->videoclub->devolverSocioProducto(1, 0);
        
        $this->assertSame(0, $this->videoclub->getNumProductosAlquilados());
        
        $producto = $this->videoclub->getProducto(0);
        $this->assertNull($producto['alquiladoBy']);
        
        $socio = $this->videoclub->getSocio(1);
        $this->assertSame(0, $socio['alquilados']);
    }

    public function testDevolverSocioProductoLanzaExcepcionSiSocioNoExiste()
    {
        $this->videoclub->incluirDvd('Película', 10, 'ES', '16:9');
        
        $this->expectException(ClienteNoEncontradoException::class);
        
        $this->videoclub->devolverSocioProducto(999, 0);
    }

    public function testDevolverSocioProductoLanzaExcepcionSiProductoNoExiste()
    {
        $this->videoclub->incluirSocio('Juan Pérez');
        
        $this->expectException(SoporteNoEncontradoException::class);
        
        $this->videoclub->devolverSocioProducto(1, 999);
    }

    public function testDevolverSocioProductoLanzaExcepcionSiNoAlquiladoPorEseSocio()
    {
        $this->videoclub->incluirSocio('Juan Pérez');
        $this->videoclub->incluirSocio('María García');
        $this->videoclub->incluirDvd('El Padrino', 14.99, 'Español', '16:9');
        
        $this->videoclub->alquilaSocioProducto(1, 0);
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('no está alquilado por el socio');
        
        $this->videoclub->devolverSocioProducto(2, 0);
    }

    public function testDevolverProductoNoAlquiladoLanzaExcepcion()
    {
        $this->videoclub->incluirSocio('Juan Pérez');
        $this->videoclub->incluirDvd('El Padrino', 14.99, 'Español', '16:9');
        
        // Devolver un producto que no está alquilado - DEBERÍA lanzar excepción
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('no está alquilado por el socio');
        
        $this->videoclub->devolverSocioProducto(1, 0);
    }

    // ==================== PRUEBAS DE DEVOLUCIÓN MÚLTIPLE ====================
    
    public function testDevolverSocioProductosExitoso()
{
    $this->videoclub->incluirSocio('Juan Pérez', null, 3);

    // Crear y alquilar 3 productos
    for ($i = 0; $i < 3; $i++) {
        $this->videoclub->incluirDvd("Película $i", 10, 'ES', '16:9');
    }

    $this->videoclub->alquilarSocioProductos(1, [0, 1, 2]);
    $this->assertSame(3, $this->videoclub->getNumProductosAlquilados());

    // Devolver 2 productos
    $this->videoclub->devolverSocioProductos(1, [0, 1]);

    $this->assertSame(1, $this->videoclub->getNumProductosAlquilados());

    $socio = $this->videoclub->getSocio(1);
    $this->assertSame(1, $socio['alquilados']);

    // Verificar estado de cada producto
    $this->assertNull($this->videoclub->getProducto(0)['alquiladoBy']);
    $this->assertNull($this->videoclub->getProducto(1)['alquiladoBy']);
    $this->assertSame(1, $this->videoclub->getProducto(2)['alquiladoBy']);
}

public function testDevolverSocioProductosMezclados()
{
    $this->videoclub->incluirSocio('Juan Pérez', null, 3);

    // Crear 3 productos
    for ($i = 0; $i < 3; $i++) {
        $this->videoclub->incluirDvd("Película $i", 10, 'ES', '16:9');
    }
    
    // Alquilar productos 0 y 2
    $this->videoclub->alquilaSocioProducto(1, 0);
    $this->videoclub->alquilaSocioProducto(1, 2);
    
    $this->assertSame(2, $this->videoclub->getNumProductosAlquilados());
    
    // Intentar devolver [0, 1, 2] - el 1 no está alquilado
    $this->videoclub->devolverSocioProductos(1, [0, 1, 2]);
    
    // Solo deberían haberse devuelto 0 y 2
    $this->assertSame(0, $this->videoclub->getNumProductosAlquilados());
    
    // Producto 1 nunca estuvo alquilado
    $this->assertNull($this->videoclub->getProducto(1)['alquiladoBy']);
}

    public function testDevolverSocioProductosLanzaExcepcionSiSocioNoExiste()
    {
        // Según tu implementación, SÍ lanza excepción
        $this->expectException(ClienteNoEncontradoException::class);
        
        $this->videoclub->devolverSocioProductos(999, [0]);
    }

    public function testDevolverSocioProductosLanzaExcepcionSiProductoNoExiste()
    {
        $this->videoclub->incluirSocio('Juan Pérez');
        
        // Según tu implementación, SÍ lanza excepción
        $this->expectException(SoporteNoEncontradoException::class);
        
        $this->videoclub->devolverSocioProductos(1, [999]);
    }

    // ==================== PRUEBAS DE LISTADO Y ESTADÍSTICAS ====================
    
    public function testListarProductos()
    {
        $this->videoclub->incluirDvd('El Padrino', 14.99, 'Español', '16:9');
        $this->videoclub->incluirSocio('Juan Pérez');
        $this->videoclub->alquilaSocioProducto(1, 0);
        
        $output = $this->videoclub->listarProductos();
        
        $this->assertIsString($output);
        $this->assertStringContainsString('El Padrino', $output);
        $this->assertStringContainsString('dvd', $output);
        $this->assertStringContainsString('Alquilado por socio 1', $output);
    }

    public function testListarSocios()
    {
        $this->videoclub->incluirSocio('Juan Pérez', null, 3);
        $this->videoclub->incluirSocio('María García', null, 5);
        
        $output = $this->videoclub->listarSocios();
        
        $this->assertIsString($output);
        $this->assertStringContainsString('Juan Pérez', $output);
        $this->assertStringContainsString('María García', $output);
        $this->assertStringContainsString('límite 3', $output);
        $this->assertStringContainsString('límite 5', $output);
    }

    public function testMostrarEstadisticas()
    {
        $this->videoclub->incluirSocio('Juan Pérez');
        $this->videoclub->incluirSocio('María García');
        $this->videoclub->incluirDvd('Película 1', 10, 'ES', '16:9');
        $this->videoclub->incluirDvd('Película 2', 10, 'ES', '16:9');
        
        $this->videoclub->alquilaSocioProducto(1, 0);
        $this->videoclub->alquilaSocioProducto(2, 1);
        
        $output = $this->videoclub->mostrarEstadisticas();
        
        $this->assertIsString($output);
        $this->assertStringContainsString('VideoClub Premier', $output);
        $this->assertStringContainsString('Total productos: 2', $output);
        $this->assertStringContainsString('Productos alquilados: 2', $output);
        $this->assertStringContainsString('Total socios: 2', $output);
        $this->assertStringContainsString('Total alquileres realizados: 2', $output);
    }

    public function testCicloCompletoAlquilerDevolucion()
    {
        // Setup
        $this->videoclub->incluirSocio('Juan Pérez', null, 3);
        
        for ($i = 0; $i < 3; $i++) {
            $this->videoclub->incluirDvd("Película $i", 10, 'ES', '16:9');
        }
        
        // Alquilar todos
        $this->videoclub->alquilarSocioProductos(1, [0, 1, 2]);
        $this->assertSame(3, $this->videoclub->getNumProductosAlquilados());
        $this->assertSame(3, $this->videoclub->getNumTotalAlquileres());
        
        // Devolver uno
        $this->videoclub->devolverSocioProducto(1, 1);
        $this->assertSame(2, $this->videoclub->getNumProductosAlquilados());
        
        // Alquilar otro diferente
        $this->videoclub->incluirDvd('Película Nueva', 12, 'ES', '16:9', 10);
        $this->videoclub->alquilaSocioProducto(1, 10);
        $this->assertSame(3, $this->videoclub->getNumProductosAlquilados());
        $this->assertSame(4, $this->videoclub->getNumTotalAlquileres());
        
        // Devolver todos
        $this->videoclub->devolverSocioProductos(1, [0, 2, 10]);
        $this->assertSame(0, $this->videoclub->getNumProductosAlquilados());
        
        // Estadísticas finales
        $socio = $this->videoclub->getSocio(1);
        $this->assertSame(0, $socio['alquilados']);
        
        $this->assertSame(4, $this->videoclub->getNumProductos());
        $this->assertSame(1, $this->videoclub->getNumSocios());
        $this->assertSame(4, $this->videoclub->getNumTotalAlquileres());
    }

    public function testIdsAutoincrementalesCorrectos()
    {
        // Socios con IDs personalizados
        $this->videoclub->incluirSocio('Socio 1', 10);
        $this->videoclub->incluirSocio('Socio 2', 20);
        $this->videoclub->incluirSocio('Socio 3'); // ID auto: 21
        
        // Productos con IDs personalizados
        $this->videoclub->incluirDvd('DVD 1', 10, 'ES', '16:9', 100);
        $this->videoclub->incluirDvd('DVD 2', 10, 'ES', '16:9', 200);
        
        // Verificar que el tercer socio existe
        $socio21 = $this->videoclub->getSocio(21);
        $this->assertNotNull($socio21);
        $this->assertSame('Socio 3', $socio21['nombre']);
    }

    public function testAlquilerConSociosMultiples()
    {
        $this->videoclub->incluirSocio('Juan', null, 2);
        $this->videoclub->incluirSocio('María', null, 2);
        
        $this->videoclub->incluirDvd('Película A', 10, 'ES', '16:9');
        $this->videoclub->incluirDvd('Película B', 10, 'ES', '16:9');
        $this->videoclub->incluirDvd('Película C', 10, 'ES', '16:9');
        
        // Juan alquila A y B
        $this->videoclub->alquilaSocioProducto(1, 0);
        $this->videoclub->alquilaSocioProducto(1, 1);
        
        // María alquila C
        $this->videoclub->alquilaSocioProducto(2, 2);
        
        $this->assertSame(3, $this->videoclub->getNumProductosAlquilados());
        $this->assertSame(3, $this->videoclub->getNumTotalAlquileres());
        
        // Verificar alquileres específicos
        $this->assertSame(1, $this->videoclub->getProducto(0)['alquiladoBy']);
        $this->assertSame(1, $this->videoclub->getProducto(1)['alquiladoBy']);
        $this->assertSame(2, $this->videoclub->getProducto(2)['alquiladoBy']);
    }
}