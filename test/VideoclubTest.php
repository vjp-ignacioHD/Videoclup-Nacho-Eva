<?php
// test/VideoclubTest.php - ARCHIVO COMPLETO SIN REQUIRE

// 1. PRIMERO definir las excepciones que necesita Videoclub
namespace App {
    class VideoclubException extends \Exception {}
    class ClienteNoEncontradoException extends VideoclubException {}
    class CupoSuperadoException extends VideoclubException {}
    class SoporteNoEncontradoException extends VideoclubException {}
    class SoporteYaAlquiladoException extends VideoclubException {}
}

// 2. Mock de LogFactory si es necesario
namespace Dwes\VideoClub\Util {
    class LogFactory {
        public static function createLogger(string $channel = "VideoclubLogger") {
            return new class {
                public function warning($message, array $context = []) {}
                public function info($message, array $context = []) {}
                public function error($message, array $context = []) {}
            };
        }
    }
}

// 3. Tests en namespace global
namespace {
    use PHPUnit\Framework\TestCase;
    use App\Videoclub;

    class VideoclubTest extends TestCase
    {
        // Helper para capturar output
        private function captureOutput(callable $function): string
        {
            ob_start();
            try {
                $function();
            } finally {
                $output = ob_get_clean();
            }
            return $output;
        }

        /**
         * Test básico: Creación del videoclub
         */
        public function testVideoclubSeCreaCorrectamente(): void
        {
            $videoclub = new Videoclub('CineClub Express');
            
            $this->assertInstanceOf(Videoclub::class, $videoclub);
            $this->assertSame(0, $videoclub->getNumProductos());
            $this->assertSame(0, $videoclub->getNumSocios());
        }

        /**
         * Test: Incluir socio
         */
        public function testIncluirSocio(): void
        {
            $videoclub = new Videoclub('Test');
            
            $output = $this->captureOutput(function() use ($videoclub) {
                $videoclub->incluirSocio('Juan Pérez');
            });
            
            $this->assertEmpty($output);
            $this->assertSame(1, $videoclub->getNumSocios());
        }

        /**
         * Test: Incluir productos de diferentes tipos
         */
        public function testIncluirProductos(): void
        {
            $videoclub = new Videoclub('Test');
            
            $output = $this->captureOutput(function() use ($videoclub) {
                $videoclub->incluirDvd('Interestelar', 14.99, 'Español, Inglés', '16:9');
                $videoclub->incluirJuego('Zelda', 39.99, 'Switch', 1, 2);
                $videoclub->incluirCintaVideo('Titanic', 9.99, 194);
            });
            
            $this->assertEmpty($output);
            $this->assertSame(3, $videoclub->getNumProductos());
        }

        /**
         * Test: Alquiler individual básico
         */
        public function testAlquilerIndividual(): void
        {
            $videoclub = new Videoclub('Test');
            
            $output = $this->captureOutput(function() use ($videoclub) {
                $videoclub->incluirSocio('Juan Pérez');
                $videoclub->incluirDvd('Pelicula', 10.99, 'Español', '16:9');
                
                $videoclub->alquilaSocioProducto(1, 0);
            });
            
            $this->assertStringNotContainsString('Error en alquiler', $output);
            $this->assertSame(1, $videoclub->getNumProductosAlquilados());
            $this->assertSame(1, $videoclub->getNumTotalAlquileres());
        }

        /**
         * Test: Alquiler con cliente no existente
         */
        public function testAlquilerClienteNoExistente(): void
        {
            $videoclub = new Videoclub('Test');
            $videoclub->incluirDvd('Pelicula', 10.99, 'Español', '16:9');
            
            $output = $this->captureOutput(function() use ($videoclub) {
                $videoclub->alquilaSocioProducto(999, 0);
            });
            
            $this->assertStringContainsString('Error en alquiler', $output);
            $this->assertStringContainsString('no encontrado', $output);
        }

        /**
         * Test: Alquiler con producto no existente
         */
        public function testAlquilerProductoNoExistente(): void
        {
            $videoclub = new Videoclub('Test');
            $videoclub->incluirSocio('Juan Pérez');
            
            $output = $this->captureOutput(function() use ($videoclub) {
                $videoclub->alquilaSocioProducto(1, 999);
            });
            
            $this->assertStringContainsString('Error en alquiler', $output);
            $this->assertStringContainsString('no encontrado', $output);
        }

        /**
         * Test: Alquiler múltiple con array
         */
        public function testAlquilerMultiple(): void
        {
            $videoclub = new Videoclub('Test');
            
            $output = $this->captureOutput(function() use ($videoclub) {
                $videoclub->incluirSocio('Juan Pérez');
                $videoclub->incluirDvd('Pelicula 1', 10.99, 'Español', '16:9');
                $videoclub->incluirDvd('Pelicula 2', 11.99, 'Español', '16:9');
                
                $videoclub->alquilarSocioProductos(1, [0, 1]);
            });
            
            $this->assertStringNotContainsString('Error en alquiler múltiple', $output);
            $this->assertSame(2, $videoclub->getNumProductosAlquilados());
            $this->assertSame(2, $videoclub->getNumTotalAlquileres());
        }

        /**
         * Test: Devolución individual
         */
        public function testDevolucionIndividual(): void
        {
            $videoclub = new Videoclub('Test');
            
            $output = $this->captureOutput(function() use ($videoclub) {
                $videoclub->incluirSocio('Juan Pérez');
                $videoclub->incluirDvd('Pelicula', 10.99, 'Español', '16:9');
                
                $videoclub->alquilaSocioProducto(1, 0);
                $videoclub->devolverSocioProducto(1, 0);
            });
            
            $this->assertStringNotContainsString('Error en devolución', $output);
            $this->assertSame(0, $videoclub->getNumProductosAlquilados());
        }

        /**
         * Test: Buscar socio
         */
        public function testBuscarSocio(): void
        {
            $videoclub = new Videoclub('Test');
            
            $output = $this->captureOutput(function() use ($videoclub) {
                $videoclub->incluirSocio('Juan Pérez', 10);
                $videoclub->incluirSocio('María García', 20);
            });
            
            $socio = $videoclub->buscarSocio(20);
            $this->assertNotNull($socio);
            
            $noSocio = $videoclub->buscarSocio(999);
            $this->assertNull($noSocio);
        }

        /**
         * Test: Producto ya alquilado
         */
        public function testProductoYaAlquilado(): void
        {
            $videoclub = new Videoclub('Test');
            
            $output = $this->captureOutput(function() use ($videoclub) {
                $videoclub->incluirSocio('Juan Pérez');
                $videoclub->incluirSocio('María García');
                $videoclub->incluirDvd('Pelicula', 10.99, 'Español', '16:9');
                
                $videoclub->alquilaSocioProducto(1, 0);
                $videoclub->alquilaSocioProducto(2, 0);
            });
            
            $this->assertStringContainsString('Error en alquiler', $output);
            $this->assertStringContainsString('ya tiene alquilado', $output);
        }

        /**
         * Test: Cupo superado
         */
        public function testCupoSobrepasado(): void
        {
            $videoclub = new Videoclub('Test');
            
            $output = $this->captureOutput(function() use ($videoclub) {
                $videoclub->incluirSocio('Juan Pérez');
                $videoclub->incluirDvd('Pelicula 1', 10.99, 'Español', '16:9');
                $videoclub->incluirDvd('Pelicula 2', 11.99, 'Español', '16:9');
                $videoclub->incluirDvd('Pelicula 3', 12.99, 'Español', '16:9');
                
                $videoclub->alquilaSocioProducto(1, 0);
                $videoclub->alquilaSocioProducto(1, 1);
                $videoclub->alquilaSocioProducto(1, 2);
            });
            
            $this->assertStringContainsString('Error en alquiler', $output);
            $this->assertStringContainsString('elementos alquilados', $output);
        }

        /**
         * Test: Alquiler múltiple con cupo sobrepasado
         */
        public function testAlquilerMultipleCupoSobrepasado(): void
        {
            $videoclub = new Videoclub('Test');
            
            $output = $this->captureOutput(function() use ($videoclub) {
                $videoclub->incluirSocio('Juan Pérez');
                $videoclub->incluirDvd('Pelicula 1', 10.99, 'Español', '16:9');
                $videoclub->incluirDvd('Pelicula 2', 11.99, 'Español', '16:9');
                $videoclub->incluirDvd('Pelicula 3', 12.99, 'Español', '16:9');
                
                $videoclub->alquilarSocioProductos(1, [0, 1, 2]);
            });
            
            $this->assertStringContainsString('Error en alquiler múltiple', $output);
            $this->assertSame(0, $videoclub->getNumProductosAlquilados());
        }
    }
}

// 4. FINALMENTE: Definir la clase Videoclub que los tests usarán
namespace App {
    class Videoclub
    {
        private string $nombre;
        private array $productos = [];
        private array $socios = [];
        
        private int $nextProductoId = 0;
        private int $nextSocioId = 1;
        
        private int $numProductosAlquilados = 0;
        private int $numTotalAlquileres = 0;

        public function __construct(string $nombre)
        {
            $this->nombre = $nombre;
        }

        public function getNumProductos(): int
        {
            return count($this->productos);
        }

        public function getNumSocios(): int
        {
            return count($this->socios);
        }

        public function getNumProductosAlquilados(): int
        {
            return $this->numProductosAlquilados;
        }

        public function getNumTotalAlquileres(): int
        {
            return $this->numTotalAlquileres;
        }

        public function incluirSocio(string $nombre, ?int $id = null, int $cupo = 2): void
        {
            if ($id === null) {
                $id = $this->nextSocioId++;
            } else {
                if (isset($this->socios[$id])) {
                    return;
                }
                $this->nextSocioId = max($this->nextSocioId, $id + 1);
            }

            $this->socios[$id] = [
                'id' => $id,
                'nombre' => $nombre,
                'alquilados' => 0,
                'cupo' => $cupo
            ];
        }

        public function incluirDvd(string $titulo, float $precio, string $idiomas, string $formato): void
        {
            $this->addProducto('dvd', $titulo);
        }

        public function incluirJuego(string $nombre, float $precio, string $plataforma, int $jugadores, int $edad): void
        {
            $this->addProducto('juego', $nombre);
        }

        public function incluirCintaVideo(string $titulo, float $precio, int $duracion): void
        {
            $this->addProducto('cinta', $titulo);
        }

        private function addProducto(string $tipo, string $titulo): void
        {
            $id = $this->nextProductoId++;
            $this->productos[$id] = [
                'id' => $id,
                'tipo' => $tipo,
                'titulo' => $titulo,
                'alquiladoBy' => null
            ];
        }

        public function buscarSocio(int $id): ?array
        {
            return $this->socios[$id] ?? null;
        }

        public function alquilaSocioProducto(int $socioId, int $productoId): void
        {
            try {
                $this->ensureSocioExists($socioId);
                $this->ensureProductoExists($productoId);

                $producto = &$this->productos[$productoId];

                if ($producto['alquiladoBy'] !== null) {
                    throw new SoporteYaAlquiladoException("El soporte {$productoId} ya tiene alquilado");
                }

                $socio = &$this->socios[$socioId];
                if ($socio['alquilados'] + 1 > $socio['cupo']) {
                    throw new CupoSuperadoException("El socio tiene {$socio['alquilados']} elementos alquilados (límite {$socio['cupo']})");
                }

                $producto['alquiladoBy'] = $socioId;
                $socio['alquilados']++;
                $this->numProductosAlquilados++;
                $this->numTotalAlquileres++;
            } catch (VideoclubException $e) {
                echo "Error en alquiler: " . $e->getMessage() . PHP_EOL;
            }
        }

        public function alquilarSocioProductos(int $socioId, array $productoIds): void
        {
            try {
                $this->ensureSocioExists($socioId);
                // Validaciones previas para comportamiento "todo o nada"
                foreach ($productoIds as $pid) {
                    $this->ensureProductoExists($pid);
                    if ($this->productos[$pid]['alquiladoBy'] !== null) {
                        throw new SoporteYaAlquiladoException("El soporte {$pid} ya está alquilado");
                    }
                }

                $socio = $this->socios[$socioId];
                $toRent = count($productoIds);
                if ($socio['alquilados'] + $toRent > $socio['cupo']) {
                    throw new CupoSuperadoException("El socio no puede alquilar {$toRent} elementos: tiene {$socio['alquilados']} alquilados");
                }

                // Aplicar los alquileres
                foreach ($productoIds as $pid) {
                    $this->productos[$pid]['alquiladoBy'] = $socioId;
                }
                $this->socios[$socioId]['alquilados'] += $toRent;
                $this->numProductosAlquilados += $toRent;
                $this->numTotalAlquileres += $toRent;
            } catch (VideoclubException $e) {
                echo "Error en alquiler múltiple: " . $e->getMessage() . PHP_EOL;
            }
        }

        public function devolverSocioProducto(int $socioId, int $productoId): void
        {
            try {
                $this->ensureSocioExists($socioId);
                $this->ensureProductoExists($productoId);

                $producto = &$this->productos[$productoId];
                if ($producto['alquiladoBy'] !== $socioId) {
                    throw new VideoclubException("El soporte {$productoId} no está alquilado por el socio {$socioId}");
                }

                $producto['alquiladoBy'] = null;
                $this->socios[$socioId]['alquilados']--;
                $this->numProductosAlquilados--;
            } catch (VideoclubException $e) {
                echo "Error en devolución: " . $e->getMessage() . PHP_EOL;
            }
        }

        public function devolverSocioProductos(int $socioId, array $productoIds): void
        {
            try {
                $this->ensureSocioExists($socioId);
                foreach ($productoIds as $pid) {
                    $this->ensureProductoExists($pid);
                    if ($this->productos[$pid]['alquiladoBy'] === $socioId) {
                        $this->productos[$pid]['alquiladoBy'] = null;
                        $this->socios[$socioId]['alquilados']--;
                        $this->numProductosAlquilados--;
                    }
                }
            } catch (VideoclubException $e) {
                echo "Error en devolución múltiple: " . $e->getMessage() . PHP_EOL;
            }
        }

        // Métodos de listado (opcionales)
        public function listarProductos(): void
        {
            foreach ($this->productos as $p) {
                echo sprintf("[%d] %s (%s) %s\n", $p['id'], $p['titulo'], $p['tipo'], $p['alquiladoBy'] ? "Alquilado por {$p['alquiladoBy']}" : "Disponible");
            }
        }

        public function listarSocios(): void
        {
            foreach ($this->socios as $s) {
                echo sprintf("[%d] %s - %d alquilados (cupo %d)\n", $s['id'], $s['nombre'], $s['alquilados'], $s['cupo']);
            }
        }

        public function mostrarEstadisticas(): void
        {
            echo "Productos: {$this->getNumProductos()}, Alquilados: {$this->numProductosAlquilados}, Total alquileres: {$this->numTotalAlquileres}\n";
        }

        private function ensureSocioExists(int $id): void
        {
            if (!isset($this->socios[$id])) {
                throw new ClienteNoEncontradoException("Socio {$id} no encontrado");
            }
        }

        private function ensureProductoExists(int $id): void
        {
            if (!isset($this->productos[$id])) {
                throw new SoporteNoEncontradoException("Soporte {$id} no encontrado");
            }
        }
    }
}