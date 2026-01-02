<?php

namespace App;

// Importamos Monolog
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

// Importamos las excepciones personalizadas que usará esta clase
use Dwes\ProyectoVideoclub\Util\ClienteNoEncontradoException;
use Dwes\ProyectoVideoclub\Util\SoporteNoEncontradoException;
use Dwes\ProyectoVideoclub\Util\SoporteYaAlquiladoException;
use Dwes\ProyectoVideoclub\Util\CupoSuperadoException;
use Dwes\ProyectoVideoclub\Util\VideoclubException;

class Videoclub
{
    private string $nombre;
    private array $productos = [];
    private array $socios = [];
    private int $numProductos = 0;
    private int $numSocios = 0;
    private int $numProductosAlquilados = 0;
    private int $numTotalAlquileres = 0;
    private $logger; // ✅ Nueva propiedad

    // CONSTRUCTOR
    public function __construct(string $nombre)
    {
        $this->nombre = $nombre;

        $this->logger = \Dwes\VideoClub\Util\LogFactory::createLogger();
    }

    // Getters
    public function getNumProductosAlquilados(): int
    {
        return $this->numProductosAlquilados;
    }

    public function getNumTotalAlquileres(): int
    {
        return $this->numTotalAlquileres;
    }

    // Métodos de inclusión de productos
    public function incluirJuego(string $titulo, float $precio, string $consola, int $minJugadores, int $maxJugadores): void
    {
        $juego = new Juego($titulo, $this->numProductos, $precio, $consola, $minJugadores, $maxJugadores);
        $juego->metacritic = $urlMetacritic;
        $this->incluirProducto($juego);
    }

    public function incluirDvd(string $titulo, float $precio, string $idiomas, string $formatoPantalla): void
    {
        $dvd = new Dvd($titulo, $this->numProductos, $precio, $idiomas, $formatoPantalla);
        $dvd->metacritic = $urlMetacritic; // ✅ Asignar la URL
        $this->incluirProducto($dvd);
    }

    public function incluirCintaVideo(string $titulo, float $precio, int $duracion): void
    {
        $cinta = new CintaVideo($titulo, $this->numProductos, $precio, $duracion);
        $cinta->metacritic = $urlMetacritic;
        $this->incluirProducto($cinta);
    }

    private function incluirProducto(Soporte $producto): void
    {
        $this->productos[] = $producto;
        // ✅ Reemplazado por log con contexto
        $this->logger->info("Incluido soporte", [
            'num_soporte' => $this->numProductos,
            'titulo' => $producto->titulo,
            'tipo' => get_class($producto)
        ]);
        $this->numProductos++;
    }

    // ✅ MÉTODOS DE VISUALIZACIÓN → se mantienen con echo (como muestraResumen)
    public function listarProductos(): void
    {
        echo "<br><strong>Listado de los {$this->numProductos} productos disponibles:</strong><br>";
        foreach ($this->productos as $indice => $producto) {
            echo ($indice + 1) . ".- ";
            $producto->muestraResumen();
            echo "<strong>Estado:</strong> " . ($producto->getAlquilado() ? "ALQUILADO" : "DISPONIBLE") . "<br>";
            echo "<br>";
        }
    }

    public function incluirSocio(string $nombre, int $numeroSocio = 0): void
    {
        if (func_num_args() === 1) {
            $numeroSocio = $this->numSocios + 1;
        }

        $cliente = new Cliente($nombre, $numeroSocio, 2);
        $this->socios[] = $cliente;

        // ✅ Reemplazado por log con contexto
        $this->logger->info("Incluido socio", [
            'num_socio' => $this->numSocios,
            'nombre' => $nombre,
            'numero_asignado' => $numeroSocio
        ]);
        $this->numSocios++;
    }

    public function alquilaSocioProducto(int $numSocio, int $numProducto): Videoclub
    {
        try {
            $cliente = $this->buscarSocio($numSocio);
            $producto = $this->buscarProducto($numProducto);

            if (!$cliente) {
                $this->logger->warning("Cliente no encontrado al intentar alquilar", [
                    'num_socio' => $numSocio,
                    'num_producto' => $numProducto
                ]);
                throw new ClienteNoEncontradoException("Socio con número $numSocio no encontrado");
            }

            if (!$producto) {
                $this->logger->warning("Producto no encontrado al intentar alquilar", [
                    'num_socio' => $numSocio,
                    'num_producto' => $numProducto
                ]);
                throw new SoporteNoEncontradoException("Producto con número $numProducto no encontrado");
            }

            $cliente->alquilar($producto);

            $this->numProductosAlquilados++;
            $this->numTotalAlquileres++;

            // ✅ Log de éxito con contexto
            $this->logger->info("Alquiler individual completado", [
                'cliente_nombre' => $cliente->nombre,
                'cliente_num' => $numSocio,
                'producto_titulo' => $producto->titulo,
                'producto_num' => $numProducto
            ]);

        } catch (SoporteYaAlquiladoException $e) {
            echo "<br><strong>Error en alquiler:</strong> " . $e->getMessage() . "<br>";
        } catch (CupoSuperadoException $e) {
            echo "<br><strong>Error en alquiler:</strong> " . $e->getMessage() . "<br>";
        } catch (ClienteNoEncontradoException $e) {
            echo "<br><strong>Error en alquiler:</strong> " . $e->getMessage() . "<br>";
        } catch (SoporteNoEncontradoException $e) {
            echo "<br><strong>Error en alquiler:</strong> " . $e->getMessage() . "<br>";
        }

        return $this;
    }

    public function alquilarSocioProductos(int $numSocio, array $numerosProductos): Videoclub
    {
        try {
            $cliente = $this->buscarSocio($numSocio);
            if (!$cliente) {
                $this->logger->warning("Cliente no encontrado en alquiler múltiple", ['num_socio' => $numSocio]);
                throw new ClienteNoEncontradoException("Socio con número $numSocio no encontrado");
            }

            $productosAAlquilar = [];
            foreach ($numerosProductos as $numProducto) {
                $producto = $this->buscarProducto($numProducto);
                if (!$producto) {
                    $this->logger->warning("Producto no encontrado en alquiler múltiple", [
                        'num_socio' => $numSocio,
                        'num_producto' => $numProducto
                    ]);
                    throw new SoporteNoEncontradoException("Producto con número $numProducto no encontrado");
                }
                if ($producto->getAlquilado()) {
                    $this->logger->warning("Producto ya alquilado en alquiler múltiple", [
                        'num_socio' => $numSocio,
                        'num_producto' => $numProducto,
                        'titulo' => $producto->titulo
                    ]);
                    throw new SoporteYaAlquiladoException("El producto '{$producto->titulo}' (Nº $numProducto) ya está alquilado");
                }
                if (($cliente->getNumSoportesAlquilados() + count($numerosProductos)) > 2) {
                    $this->logger->warning("Cupo superado en alquiler múltiple", [
                        'num_socio' => $numSocio,
                        'cliente_nombre' => $cliente->nombre,
                        'productos_solicitados' => count($numerosProductos)
                    ]);
                    throw new CupoSuperadoException("El cliente no puede alquilar " . count($numerosProductos) . " productos. Superaría su cupo máximo de 2");
                }
                $productosAAlquilar[] = $producto;
            }

            foreach ($productosAAlquilar as $producto) {
                $cliente->alquilar($producto);
                $this->numProductosAlquilados++;
                $this->numTotalAlquileres++;
            }

            // ✅ Mensaje de éxito → reemplazado por log (no es un catch)
            $this->logger->info("Alquiler múltiple completado", [
                'cliente_nombre' => $cliente->nombre,
                'cliente_num' => $numSocio,
                'num_productos' => count($numerosProductos),
                'productos' => $numerosProductos
            ]);

        } catch (VideoclubException $e) {
            // ❌ Los mensajes en catch se mantienen con echo (son para el usuario)
            echo "<br><strong>Error en alquiler múltiple:</strong> " . $e->getMessage() . "<br>";
            echo "<em>Ningún producto ha sido alquilado debido al error.</em><br>";
        }

        return $this;
    }

    public function devolverSocioProducto(int $numSocio, int $numeroProducto): Videoclub
    {
        try {
            $cliente = $this->buscarSocio($numSocio);
            if (!$cliente) {
                $this->logger->warning("Cliente no encontrado al devolver", ['num_socio' => $numSocio]);
                throw new ClienteNoEncontradoException("Socio con número $numSocio no encontrado");
            }

            $cliente->devolver($numeroProducto);
            $this->numProductosAlquilados--;

            // ✅ Log de devolución
            $this->logger->info("Devolución individual completada", [
                'cliente_nombre' => $cliente->nombre,
                'cliente_num' => $numSocio,
                'producto_num' => $numeroProducto
            ]);

        } catch (ClienteNoEncontradoException $e) {
            echo "<br><strong>Error en devolución:</strong> " . $e->getMessage() . "<br>";
        }

        return $this;
    }

    public function devolverSocioProductos(int $numSocio, array $numerosProductos): Videoclub
    {
        try {
            $cliente = $this->buscarSocio($numSocio);
            if (!$cliente) {
                $this->logger->warning("Cliente no encontrado en devolución múltiple", ['num_socio' => $numSocio]);
                throw new ClienteNoEncontradoException("Socio con número $numSocio no encontrado");
            }

            $devolucionesExitosas = 0;
            foreach ($numerosProductos as $numeroProducto) {
                try {
                    $cliente->devolver($numeroProducto);
                    $devolucionesExitosas++;
                    $this->numProductosAlquilados--;
                } catch (\Exception $e) {
                    continue;
                }
            }

            if ($devolucionesExitosas > 0) {
                // ✅ Log de éxito
                $this->logger->info("Devolución múltiple completada", [
                    'cliente_nombre' => $cliente->nombre,
                    'cliente_num' => $numSocio,
                    'devoluciones' => $devolucionesExitosas,
                    'productos' => $numerosProductos
                ]);
            }

        } catch (ClienteNoEncontradoException $e) {
            echo "<br><strong>Error en devolución múltiple:</strong> " . $e->getMessage() . "<br>";
        }

        return $this;
    }

    // ✅ MÉTODOS DE VISUALIZACIÓN → se mantienen con echo
    public function mostrarEstadisticas(): void
    {
        echo "<br><strong> Estadísticas del Videoclub \"{$this->nombre}\":</strong><br>";
        echo "Total de productos: " . $this->numProductos . "<br>";
        echo "Productos actualmente alquilados: " . $this->numProductosAlquilados . "<br>";
        echo "Total de alquileres realizados: " . $this->numTotalAlquileres . "<br>";
        echo "Socios registrados: " . $this->numSocios . "<br>";
        if ($this->numProductos > 0) {
            $porcentajeAlquilados = ($this->numProductosAlquilados / $this->numProductos) * 100;
            echo "Porcentaje de productos alquilados: " . number_format($porcentajeAlquilados, 2) . "%<br>";
        }
    }

    public function listarSocios(): void
    {
        echo "<br><strong>Listado de {$this->numSocios} socios del videoclub:</strong><br>";
        foreach ($this->socios as $indice => $cliente) {
            echo ($indice + 1) . ".- Cliente {$indice}: " . $cliente->nombre . "<br>";
            echo "Alquileres actuales: " . $cliente->getNumSoportesAlquilados() . "<br>";
        }
    }

    public function buscarSocio(int $numeroSocio): ?Cliente
    {
        foreach ($this->socios as $cliente) {
            if ($cliente->getNumero() === $numeroSocio) {
                return $cliente;
            }
        }
        return null;
    }

    private function buscarProducto(int $numeroProducto): ?Soporte
    {
        foreach ($this->productos as $producto) {
            if ($producto->getNumero() === $numeroProducto) {
                return $producto;
            }
        }
        return null;
    }
}