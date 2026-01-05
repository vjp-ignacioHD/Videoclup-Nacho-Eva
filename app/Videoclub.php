<?php

namespace App;

use Dwes\Videoclub\Exception\ClienteNoExisteException;
use Dwes\Videoclub\Exception\SoporteNoEncontradoException;
use Dwes\Videoclub\Exception\SoporteYaAlquiladoException;
use Dwes\Videoclub\Exception\CupoSuperadoException;
use Dwes\Videoclub\Exception\VideoclubException;

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

    public function getNombre(): string
    {
        return $this->nombre;
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

    public function incluirSocio(string $nombre, ?int $id = null, int $maxAlquilerConcurrente = 2): void
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
            'maxAlquilerConcurrente' => $maxAlquilerConcurrente
        ];
    }

    public function incluirJuego(string $nombre, float $precio, string $plataforma, int $minJugadores, int $maxJugadores, ?int $id = null): void
    {
        $this->addProducto('juego', $nombre, $id);
    }

    public function incluirCintaVideo(string $titulo, float $precio, int $duracion, ?int $id = null): void
    {
        $this->addProducto('cinta', $titulo, $id);
    }

    private function addProducto(string $tipo, string $titulo, ?int $id = null): void
    {
        if ($id === null) {
            $id = $this->nextProductoId++;
        } else {
            if (isset($this->productos[$id])) {
                return;
            }
            $this->nextProductoId = max($this->nextProductoId, $id + 1);
        }

        $this->productos[$id] = [
            'id' => $id,
            'tipo' => $tipo,
            'titulo' => $titulo,
            'alquiladoBy' => null
        ];
    }

    public function getSocio(int $id): ?array
    {
        return $this->socios[$id] ?? null;
    }

    public function getProducto(int $id): ?array
    {
        return $this->productos[$id] ?? null;
    }

    public function alquilaSocioProducto(int $socioId, int $productoId): void
    {
        try {
            $this->ensureSocioExists($socioId);
            $this->ensureProductoExists($productoId);

            $producto = &$this->productos[$productoId];

            if ($producto['alquiladoBy'] !== null) {
                throw new SoporteYaAlquiladoException("El soporte {$productoId} ya está alquilado");
            }

            $socio = &$this->socios[$socioId];
            if ($socio['alquilados'] + 1 > $socio['maxAlquilerConcurrente']) {
                throw new CupoSuperadoException("El socio tiene {$socio['alquilados']} elementos alquilados (límite {$socio['maxAlquilerConcurrente']})");
            }

            $producto['alquiladoBy'] = $socioId;
            $socio['alquilados']++;
            $this->numProductosAlquilados++;
            $this->numTotalAlquileres++;
        } catch (VideoclubException $e) {
            throw $e;
        }
    }

    public function alquilarSocioProductos(int $socioId, array $productoIds): void
    {
        try {
            $this->ensureSocioExists($socioId);

            $socio = $this->socios[$socioId];
            $toRent = count($productoIds);

            if ($socio['alquilados'] + $toRent > $socio['maxAlquilerConcurrente']) {
                throw new CupoSuperadoException("El socio no puede alquilar {$toRent} elementos: tiene {$socio['alquilados']} alquilados (límite {$socio['maxAlquilerConcurrente']})");
            }

            foreach ($productoIds as $pid) {
                $this->ensureProductoExists($pid);
                if ($this->productos[$pid]['alquiladoBy'] !== null) {
                    throw new SoporteYaAlquiladoException("El soporte {$pid} ya está alquilado");
                }
            }

            foreach ($productoIds as $pid) {
                $this->productos[$pid]['alquiladoBy'] = $socioId;
            }

            $this->socios[$socioId]['alquilados'] += $toRent;
            $this->numProductosAlquilados += $toRent;
            $this->numTotalAlquileres += $toRent;
        } catch (VideoclubException $e) {
            throw $e;
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
            throw $e;
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
            throw $e;
        }
    }

    public function listarProductos(): string
    {
        $output = "Productos en el videoclub:\n";
        foreach ($this->productos as $p) {
            $estado = $p['alquiladoBy'] ? "Alquilado por socio {$p['alquiladoBy']}" : "Disponible";
            $output .= sprintf("[%d] %s (%s) - %s\n", $p['id'], $p['titulo'], $p['tipo'], $estado);
        }
        return $output;
    }

    public function listarSocios(): string
    {
        $output = "Socios del videoclub:\n";
        foreach ($this->socios as $s) {
            $output .= sprintf(
                "[%d] %s - %d alquilados (límite %d)\n",
                $s['id'],
                $s['nombre'],
                $s['alquilados'],
                $s['maxAlquilerConcurrente']
            );
        }
        return $output;
    }

    public function mostrarEstadisticas(): string
    {
        return sprintf(
            "Estadísticas del videoclub %s:\n" .
                "Total productos: %d\n" .
                "Productos alquilados: %d\n" .
                "Total socios: %d\n" .
                "Total alquileres realizados: %d\n",
            $this->nombre,
            $this->getNumProductos(),
            $this->numProductosAlquilados,
            $this->getNumSocios(),
            $this->numTotalAlquileres
        );
    }

    public function incluirDvd(string $titulo, float $precio, string $idiomas, string $formato, int $duracion, ?int $id = null): void
    {
        if ($id === null) {
            $id = $this->nextProductoId++;
        } else {
            if (isset($this->productos[$id])) {
                return;
            }
            $this->nextProductoId = max($this->nextProductoId, $id + 1);
        }

        $this->productos[$id] = [
            'id' => $id,
            'tipo' => 'dvd',
            'titulo' => $titulo,
            'precio' => $precio,
            'idiomas' => $idiomas,
            'formato' => $formato,
            'duracion' => $duracion,
            'alquiladoBy' => null
        ];
    }

    public function incluirBluray(string $titulo, float $precio, int $duracion, bool $es4k, ?int $id = null): void
    {
        if ($id === null) {
            $id = $this->nextProductoId++;
        } else {
            if (isset($this->productos[$id])) {
                return;
            }
            $this->nextProductoId = max($this->nextProductoId, $id + 1);
        }

        $this->productos[$id] = [
            'id' => $id,
            'tipo' => 'bluray',
            'titulo' => $titulo,
            'precio' => $precio,
            'duracion' => $duracion,
            'es4k' => $es4k,
            'alquiladoBy' => null
        ];
    }

    private function ensureSocioExists(int $id): void
    {
        if (!isset($this->socios[$id])) {
            throw new ClienteNoExisteException("Socio {$id} no encontrado");
        }
    }

    private function ensureProductoExists(int $id): void
    {
        if (!isset($this->productos[$id])) {
            throw new SoporteNoEncontradoException("Soporte {$id} no encontrado");
        }
    }
}
