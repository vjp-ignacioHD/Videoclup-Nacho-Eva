<?php

// Declaramos el namespace para esta clase
namespace Dwes\ProyectoVideoclub;

// Importamos las excepciones personalizadas que usará esta clase
use Dwes\ProyectoVideoclub\Util\ClienteNoEncontradoException;
use Dwes\ProyectoVideoclub\Util\SoporteNoEncontradoException;
use Dwes\ProyectoVideoclub\Util\SoporteYaAlquiladoException;
use Dwes\ProyectoVideoclub\Util\CupoSuperadoException;

// Esta clase representa el videoclub, que gestiona productos y clientes
class Videoclub
{
    private string $nombre;
    private array $productos = [];        // Array donde se guardan todos los soportes disponibles
    private array $socios = [];           // Array donde se guardan todos los clientes registrados
    private int $numProductos = 0;
    private int $numSocios = 0;
    private int $numProductosAlquilados = 0;
    private int $numTotalAlquileres = 0;

    // CONSTRUCTOR
    public function __construct(string $nombre)
    {
        $this->nombre = $nombre;
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

    // Este método crea un objeto Juego y lo añade al videoclub
    public function incluirJuego(string $titulo, float $precio, string $consola, int $minJugadores, int $maxJugadores): void
    {
        $juego = new Juego($titulo, $this->numProductos, $precio, $consola, $minJugadores, $maxJugadores);
        $this->incluirProducto($juego);
    }

    // Este método crea un objeto Dvd y lo añade al videoclub
    public function incluirDvd(string $titulo, float $precio, string $idiomas, string $formatoPantalla): void
    {
        $dvd = new Dvd($titulo, $this->numProductos, $precio, $idiomas, $formatoPantalla);
        $this->incluirProducto($dvd);
    }

    // Este método crea un objeto CintaVideo y lo añade al videoclub
    public function incluirCintaVideo(string $titulo, float $precio, int $duracion): void
    {
        $cinta = new CintaVideo($titulo, $this->numProductos, $precio, $duracion);
        $this->incluirProducto($cinta);
    }

    // Este método añade cualquier soporte al array de productos
    private function incluirProducto(Soporte $producto): void
    {
        $this->productos[] = $producto;
        echo "<br>Incluido soporte " . $this->numProductos . "<br>";
        $this->numProductos++;
    }

    // Este método muestra todos los productos disponibles en el videoclub
    public function listarProductos(): void
    {
        echo "<br><strong>Listado de los {$this->numProductos} productos disponibles:</strong><br>";
        foreach ($this->productos as $indice => $producto) {
            echo ($indice + 1) . ".- ";
            $producto->muestraResumen(); // Cada soporte muestra su información específica
            echo "<strong>Estado:</strong> " . ($producto->getAlquilado() ? "ALQUILADO" : "DISPONIBLE") . "<br>";
            echo "<br>";
        }
    }

    // Este método añade un nuevo cliente al videoclub
    public function incluirSocio(string $nombre, int $numeroSocio = 0): void
    {
        // Si solo se ha pasado el nombre, asignamos el número automáticamente
        if (func_num_args() === 1) {
            $numeroSocio = $this->numSocios + 1;
        }

        $cliente = new Cliente($nombre, $numeroSocio, 2);
        $this->socios[] = $cliente;

        echo "<br>Incluido socio " . $this->numSocios . "<br>";
        $this->numSocios++;
    }

    // Este método permite que un socio alquile un producto por su número
    public function alquilaSocioProducto(int $numSocio, int $numProducto): Videoclub
    {
        try {
            $cliente = $this->buscarSocio($numSocio);
            $producto = $this->buscarProducto($numProducto);

            if (!$cliente) {
                throw new ClienteNoEncontradoException("Socio con número $numSocio no encontrado");
            }

            if (!$producto) {
                throw new SoporteNoEncontradoException("Producto con número $numProducto no encontrado");
            }

            // Esta llamada puede lanzar SoporteYaAlquiladoException o CupoSuperadoException
            $cliente->alquilar($producto);

            $this->numProductosAlquilados++;
            $this->numTotalAlquileres++;
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

    // Muestra estadísticas del videoclub
    public function mostrarEstadisticas(): void
    {
        echo "<br><strong>📊 Estadísticas del Videoclub \"{$this->nombre}\":</strong><br>";
        echo "Total de productos: " . $this->numProductos . "<br>";
        echo "Productos actualmente alquilados: " . $this->numProductosAlquilados . "<br>";
        echo "Total de alquileres realizados: " . $this->numTotalAlquileres . "<br>";
        echo "Socios registrados: " . $this->numSocios . "<br>";

        // Calcular porcentaje de productos alquilados
        if ($this->numProductos > 0) {
            $porcentajeAlquilados = ($this->numProductosAlquilados / $this->numProductos) * 100;
            echo "Porcentaje de productos alquilados: " . number_format($porcentajeAlquilados, 2) . "%<br>";
        }
    }

    // Este método muestra todos los socios registrados y cuántos alquileres tienen
    public function listarSocios(): void
    {
        echo "<br><strong>Listado de {$this->numSocios} socios del videoclub:</strong><br>";
        foreach ($this->socios as $indice => $cliente) {
            echo ($indice + 1) . ".- Cliente {$indice}: " . $cliente->nombre . "<br>";
            echo "Alquileres actuales: " . $cliente->getNumSoportesAlquilados() . "<br>";
        }
    }

    // Este método busca un cliente por su número de socio
    public function buscarSocio(int $numeroSocio): ?Cliente
    {
        foreach ($this->socios as $cliente) {
            if ($cliente->getNumero() === $numeroSocio) {
                return $cliente;
            }
        }
        return null;
    }

    // Este método busca un producto por su número
    private function buscarProducto(int $numeroProducto): ?Soporte
    {
        foreach ($this->productos as $producto) {
            if ($producto->getNumero() === $numeroProducto) {
                return $producto;
            }
        }
        return null; // Si no se encuentra, devuelve null
    }
}
