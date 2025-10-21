<?php

namespace Dwes\ProyectoVideoclub;

use Dwes\ProyectoVideoclub\SoporteYaAlquiladoException;
use Dwes\ProyectoVideoclub\CupoSuperadoException;

// Esta clase representa el videoclub, que gestiona productos y clientes
class Videoclub
{
    private string $nombre;
    private array $productos = [];        // Array donde se guardan todos los soportes disponibles
    private array $socios = [];           // Array donde se guardan todos los clientes registrados
    private int $numProductos = 0;
    private int $numSocios = 0;

    // CONSTRUCTOR
    public function __construct(string $nombre)
    {
        $this->nombre = $nombre;
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
    public function alquilaSocioProducto(int $numSocio, int $numProducto): Videoclub // Cambia el tipo de retorno
{
    $cliente = $this->buscarSocio($numSocio);
    $producto = $this->buscarProducto($numProducto);

    if ($cliente && $producto) {
        $cliente->alquilar($producto); // Aquí ya se imprime el resultado
    } else {
        echo "<br>Error: Socio o producto no encontrado.<br>";
    }

    return $this; // Devuelve $this para encadenar
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

