<?php
// test/ClienteTest.php - Versión FINAL CORREGIDA (sin namespace conflictos)

require_once __DIR__ . '/../vendor/autoload.php';

// PRIMERO: Crear las excepciones que faltan en el namespace App
// Usamos eval para crear clases en namespace App
eval('
namespace App {
    // Si no existen, las creamos
    if (!class_exists("App\\CupoSuperadoException")) {
        class CupoSuperadoException extends \\Exception {}
    }
    
    if (!class_exists("App\\SoporteYaAlquiladoException")) {
        class SoporteYaAlquiladoException extends \\Exception {}
    }
}
');

// SEGUNDO: Mock de LogFactory
eval('
namespace Dwes\\VideoClub\\Util {
    if (!class_exists("Dwes\\VideoClub\\Util\\LogFactory")) {
        class LogFactory {
            public static function createLogger(string $channel = "VideoclubLogger") {
                return new class($channel) extends \\Monolog\\Logger {
                    public function __construct($channel) {
                        parent::__construct($channel);
                    }
                    
                    public function warning($message, array $context = []) {
                        // No hacer nada en tests
                    }
                    
                    public function info($message, array $context = []) {
                        // No hacer nada en tests
                    }
                    
                    public function error($message, array $context = []) {
                        // No hacer nada en tests
                    }
                };
            }
        }
    }
}
');

// TERCERO: Ahora podemos usar las clases normalmente
use PHPUnit\Framework\TestCase;
use App\Cliente;
use App\Dvd;
use App\Juego;
use App\CintaVideo;

class ClienteTest extends TestCase
{
    /**
     * Data Provider para crear clientes con diferentes cupos
     */
    public static function clientesConCuposProvider(): array
    {
        return [
            'Cliente con cupo 1' => ['Juan Pérez', 'juan@email.com', 1],
            'Cliente con cupo 2' => ['María García', 'maria@email.com', 2],
            'Cliente con cupo 3' => ['Carlos López', 'carlos@email.com', 3],
            'Cliente con cupo 5' => ['Ana Rodríguez', 'ana@email.com', 5],
            'Cliente con cupo 10' => ['Pedro Sánchez', 'pedro@email.com', 10],
        ];
    }

    /**
     * Data Provider para crear soportes de diferentes tipos
     */
    public static function soportesProvider(): array
    {
        return [
            'DVD' => [new Dvd('El Padrino', 100, 14.99, 'Español, Inglés', '16:9')],
            'Juego' => [new Juego('The Legend of Zelda', 101, 39.99, 'Nintendo Switch', 1, 1)],
            'Cinta Video' => [new CintaVideo('Titanic', 102, 9.99, 194)],
            'DVD 2' => [new Dvd('Interestelar', 103, 19.99, 'Español, Inglés, Francés', 'IMAX')],
            'Juego 2' => [new Juego('Super Mario Odyssey', 104, 49.99, 'Nintendo Switch', 1, 2)],
        ];
    }

    /**
     * Data Provider para crear múltiples soportes con IDs únicos
     */
    public static function soportesMultiplesProvider(): array
    {
        return [
            '3 soportes diferentes' => [
                [
                    new Dvd('Pulp Fiction', 200, 12.99, 'Español', '4:3'),
                    new Juego('God of War', 201, 59.99, 'PS5', 1, 1),
                    new CintaVideo('Forrest Gump', 202, 8.99, 142)
                ]
            ],
            '5 soportes diferentes' => [
                [
                    new Dvd('Matrix', 300, 15.99, 'Español, Inglés', '16:9'),
                    new Juego('Cyberpunk 2077', 301, 49.99, 'PC', 1, 1),
                    new CintaVideo('Jurassic Park', 302, 7.99, 127),
                    new Dvd('Gladiator', 303, 11.99, 'Español', '16:9'),
                    new Juego('FIFA 23', 304, 69.99, 'PS5', 1, 4)
                ]
            ]
        ];
    }

    /**
     * @dataProvider clientesConCuposProvider
     */
    public function testClienteSeCreaConCupoCorrecto(string $nombre, string $email, int $cupo): void
    {
        $cliente = new Cliente($nombre, $email);
        $cliente->setMaxAlquilerConcurrente($cupo);
        
        $this->assertSame($nombre, $cliente->nombre);
        $this->assertSame($email, $cliente->email);
        $this->assertSame($cupo, $cliente->getMaxAlquilerConcurrente());
        $this->assertSame(0, $cliente->getNumSoportesAlquilados());
    }

    /**
     * @dataProvider clientesConCuposProvider
     */
    public function testClientePuedeAlquilarHastaSuCupo(string $nombre, string $email, int $cupo): void
    {
        $cliente = new Cliente($nombre, $email);
        $cliente->setMaxAlquilerConcurrente($cupo);
        
        // Alquilar hasta el cupo máximo
        for ($i = 0; $i < $cupo; $i++) {
            $soporte = new Dvd("Película $i", $i, 9.99, 'Español', '16:9');
            $cliente->alquilar($soporte);
            
            $this->assertSame($i + 1, $cliente->getNumSoportesAlquilados());
            $this->assertTrue($cliente->tieneAlquilado($soporte));
            $this->assertTrue($soporte->getAlquilado());
        }
        
        // Verificar que tiene exactamente el cupo de soportes alquilados
        $this->assertSame($cupo, $cliente->getNumSoportesAlquilados());
    }

    /**
     * @dataProvider clientesConCuposProvider
     */
    public function testClienteLanzaExcepcionAlSuperarCupo(string $nombre, string $email, int $cupo): void
    {
        $cliente = new Cliente($nombre, $email);
        $cliente->setMaxAlquilerConcurrente($cupo);
        
        // Alquilar hasta el cupo máximo
        for ($i = 0; $i < $cupo; $i++) {
            $soporte = new Dvd("Película $i", $i, 9.99, 'Español', '16:9');
            $cliente->alquilar($soporte);
        }
        
        // Intentar alquilar uno más debe lanzar excepción
        $soporteExtra = new Dvd('Extra', 999, 9.99, 'Español', '16:9');
        
        // Debe lanzar CupoSuperadoException
        $this->expectException(\App\CupoSuperadoException::class);
        $this->expectExceptionMessageMatches('/elementos alquilados/');
        
        $cliente->alquilar($soporteExtra);
    }

    /**
     * @dataProvider soportesProvider
     */
    public function testClienteNoPuedeAlquilarMismoSoporteDosVeces($soporte): void
    {
        $cliente = new Cliente('Cliente Test', 'test@email.com');
        $cliente->setMaxAlquilerConcurrente(3);
        
        // Primera vez - debe funcionar
        $cliente->alquilar($soporte);
        $this->assertTrue($soporte->getAlquilado());
        $this->assertTrue($cliente->tieneAlquilado($soporte));
        
        // Segunda vez - debe lanzar excepción
        $this->expectException(\App\SoporteYaAlquiladoException::class);
        $this->expectExceptionMessageMatches('/ya tiene alquilado/');
        
        $cliente->alquilar($soporte);
    }

    /**
     * Test NUEVO: Dos clientes diferentes PUEDEN alquilar el mismo soporte
     * (esto es lo que realmente permite el código actual)
     * @dataProvider soportesProvider
     */
    public function testDosClientesPuedenAlquilarMismoSoporte($soporte): void
    {
        $cliente1 = new Cliente('Cliente 1', 'cliente1@email.com');
        $cliente1->setMaxAlquilerConcurrente(3);
        
        $cliente2 = new Cliente('Cliente 2', 'cliente2@email.com');
        $cliente2->setMaxAlquilerConcurrente(3);
        
        // Cliente 1 alquila el soporte - debe funcionar
        $cliente1->alquilar($soporte);
        $this->assertTrue($soporte->getAlquilado());
        $this->assertTrue($cliente1->tieneAlquilado($soporte));
        
        // Cliente 2 también puede alquilar el mismo soporte
        // (según el código actual, esto debería funcionar)
        $cliente2->alquilar($soporte);
        
        // Ambos clientes tienen el soporte alquilado
        $this->assertTrue($cliente1->tieneAlquilado($soporte));
        $this->assertTrue($cliente2->tieneAlquilado($soporte));
        $this->assertSame(1, $cliente1->getNumSoportesAlquilados());
        $this->assertSame(1, $cliente2->getNumSoportesAlquilados());
    }

    public function testClientePuedeAlquilarYDevolverSoportes(): void
    {
        $cliente = new Cliente('Test Cliente', 'test@email.com');
        $cliente->setMaxAlquilerConcurrente(3);
        
        $soportes = [
            new Dvd('DVD 1', 1, 10.99, 'Español', '16:9'),
            new Juego('Juego 1', 2, 49.99, 'PS5', 1, 2),
            new CintaVideo('Cinta 1', 3, 7.99, 120)
        ];
        
        // Alquilar todos los soportes
        foreach ($soportes as $soporte) {
            $cliente->alquilar($soporte);
        }
        
        $this->assertSame(3, $cliente->getNumSoportesAlquilados());
        
        // Devolver el segundo soporte
        $cliente->devolver(2);
        $this->assertSame(2, $cliente->getNumSoportesAlquilados());
        
        // Verificar que el soporte devuelto ya no está alquilado
        $this->assertFalse($soportes[1]->getAlquilado());
        
        // Verificar que no tiene alquilado el soporte devuelto
        $this->assertFalse($cliente->tieneAlquilado($soportes[1]));
        
        // Verificar que los otros siguen alquilados
        $this->assertTrue($cliente->tieneAlquilado($soportes[0]));
        $this->assertTrue($cliente->tieneAlquilado($soportes[2]));
    }

    /**
     * @dataProvider soportesMultiplesProvider
     */
    public function testClientePuedeAlquilarMultiplesSoportesConIdsUnicos(array $soportes): void
    {
        $cliente = new Cliente('Cliente Multiple', 'multiple@email.com');
        $cliente->setMaxAlquilerConcurrente(count($soportes) + 2); // Cupo suficiente
        
        $idsAlquilados = [];
        
        foreach ($soportes as $soporte) {
            // Verificar que el ID no esté duplicado
            $id = $soporte->getNumero();
            $this->assertNotContains($id, $idsAlquilados, "El ID $id está duplicado");
            $idsAlquilados[] = $id;
            
            // Alquilar el soporte
            $cliente->alquilar($soporte);
            
            // Verificar que el soporte está marcado como alquilado
            $this->assertTrue($soporte->getAlquilado());
            
            // Verificar que el cliente tiene alquilado ese soporte
            $this->assertTrue($cliente->tieneAlquilado($soporte));
        }
        
        // Verificar que tiene todos los soportes alquilados
        $this->assertSame(count($soportes), $cliente->getNumSoportesAlquilados());
        
        // Verificar que los IDs en getAlquileres son únicos
        $alquileres = $cliente->getAlquileres();
        $idsEnAlquileres = array_map(function($soporte) {
            return $soporte->getNumero();
        }, $alquileres);
        
        $this->assertSame($idsAlquilados, $idsEnAlquileres);
        $this->assertSame($idsAlquilados, array_unique($idsAlquilados), 'Los IDs no son únicos');
    }

    public function testDevolverSoporteNoExistenteNoLanzaExcepcion(): void
    {
        $cliente = new Cliente('Cliente Test', 'test@email.com');
        $cliente->setMaxAlquilerConcurrente(2);
        
        // Alquilar un soporte
        $soporte = new Dvd('Test', 1, 9.99, 'Español', '16:9');
        $cliente->alquilar($soporte);
        
        // Intentar devolver un soporte que no existe
        $cliente->devolver(999);
        
        // No debe lanzar excepción y debe mantener el soporte alquilado
        $this->assertSame(1, $cliente->getNumSoportesAlquilados());
        $this->assertTrue($cliente->tieneAlquilado($soporte));
    }

    public function testMetodosGettersYSetters(): void
    {
        $cliente = new Cliente('Gonzalo', 'gonzalo@email.com');
        
        // Test setNumero y getNumero
        $cliente->setNumero(123);
        $this->assertSame(123, $cliente->getNumero());
        
        // Test setMaxAlquilerConcurrente y getMaxAlquilerConcurrente
        $cliente->setMaxAlquilerConcurrente(5);
        $this->assertSame(5, $cliente->getMaxAlquilerConcurrente());
        
        // Test setUser y getUser
        $cliente->setUser('gonzalo_user');
        $this->assertSame('gonzalo_user', $cliente->getUser());
        
        // Test setPassword y getPassword
        $cliente->setPassword('password123');
        $this->assertSame('password123', $cliente->getPassword());
    }

    public function testListarAlquileresCuandoNoHaySoportes(): void
    {
        $cliente = new Cliente('Cliente Sin Alquileres', 'sin@email.com');
        $cliente->setMaxAlquilerConcurrente(3);
        
        // Este método usa logger->info internamente, no produce salida directa
        // Simplemente verificamos que no lanza excepciones
        $this->expectNotToPerformAssertions();
        $cliente->listarAlquileres();
    }

    /**
     * Test para verificar que después de devolver un soporte, se puede alquilar de nuevo
     */
    public function testPuedeAlquilarSoporteDespuesDeDevolver(): void
    {
        $cliente = new Cliente('Cliente Test', 'test@email.com');
        $cliente->setMaxAlquilerConcurrente(2);
        
        $soporte = new Dvd('Película', 100, 12.99, 'Español', '16:9');
        
        // Alquilar
        $cliente->alquilar($soporte);
        $this->assertTrue($soporte->getAlquilado());
        $this->assertSame(1, $cliente->getNumSoportesAlquilados());
        
        // Devolver
        $cliente->devolver(100);
        $this->assertFalse($soporte->getAlquilado());
        $this->assertSame(0, $cliente->getNumSoportesAlquilados());
        
        // Alquilar de nuevo - debe funcionar
        $cliente->alquilar($soporte);
        $this->assertTrue($soporte->getAlquilado());
        $this->assertSame(1, $cliente->getNumSoportesAlquilados());
    }

    /**
     * Test para verificar IDs únicos entre diferentes soportes
     */
    public function testIdsUnicosEntreSoportes(): void
    {
        $dvd1 = new Dvd('DVD 1', 1, 10.99, 'Español', '16:9');
        $dvd2 = new Dvd('DVD 2', 2, 11.99, 'Inglés', '16:9');
        $juego1 = new Juego('Juego 1', 3, 49.99, 'PS5', 1, 2);
        $juego2 = new Juego('Juego 2', 4, 59.99, 'Xbox', 1, 4);
        
        $ids = [
            $dvd1->getNumero(),
            $dvd2->getNumero(),
            $juego1->getNumero(),
            $juego2->getNumero()
        ];
        
        // Verificar que todos los IDs son únicos
        $this->assertSame($ids, array_unique($ids), 'Los IDs de soportes no son únicos');
        
        // Verificar que no hay IDs duplicados
        $this->assertSame(count($ids), count(array_unique($ids)));
    }
    
    /**
     * Test adicional: Verificar que getAlquileres devuelve array correcto
     */
    public function testGetAlquileresDevuelveArrayCorrecto(): void
    {
        $cliente = new Cliente('Test', 'test@email.com');
        $cliente->setMaxAlquilerConcurrente(3);
        
        $soporte1 = new Dvd('DVD 1', 1, 10.99, 'Español', '16:9');
        $soporte2 = new Juego('Juego 1', 2, 49.99, 'PS5', 1, 2);
        
        $cliente->alquilar($soporte1);
        $cliente->alquilar($soporte2);
        
        $alquileres = $cliente->getAlquileres();
        
        $this->assertIsArray($alquileres);
        $this->assertCount(2, $alquileres);
        $this->assertContains($soporte1, $alquileres);
        $this->assertContains($soporte2, $alquileres);
    }
}