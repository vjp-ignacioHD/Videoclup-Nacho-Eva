<?php

namespace App;

// Importamos las excepciones personalizadas que usará esta clase
use Dwes\ProyectoVideoclub\Util\ClienteNoEncontradoException;
use Dwes\ProyectoVideoclub\Util\SoporteNoEncontradoException;
use Dwes\ProyectoVideoclub\Util\SoporteYaAlquiladoException;
use Dwes\ProyectoVideoclub\Util\CupoSuperadoException;
use Dwes\ProyectoVideoclub\Util\VideoclubException;

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

    // En este método he consultado un poco la IA

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

    // Método que permite alquilar múltiples productos a un socio en una sola operación
    public function alquilarSocioProductos(int $numSocio, array $numerosProductos): Videoclub
    {
        try {
            // Buscar el cliente en el sistema
            $cliente = $this->buscarSocio($numSocio);
            
            // Verificar que el cliente existe
            if (!$cliente) {
                throw new ClienteNoEncontradoException("Socio con número $numSocio no encontrado");
            }

            // Comprobamos que todos los productos están disponibles
            $productosAAlquilar = [];
            foreach ($numerosProductos as $numProducto) {
                // Buscar el producto en el inventario
                $producto = $this->buscarProducto($numProducto);
                
                // Verificar que el producto existe
                if (!$producto) {
                    throw new SoporteNoEncontradoException("Producto con número $numProducto no encontrado");
                }
                
                // Verificar que el producto no está ya alquilado
                if ($producto->getAlquilado()) {
                    throw new SoporteYaAlquiladoException("El producto '{$producto->titulo}' (Nº $numProducto) ya está alquilado");
                }
                
                // Verificamos que el cliente tiene cupo suficiente para todos los productos solicitados
                if (($cliente->getNumSoportesAlquilados() + count($numerosProductos)) > 2) {
                    throw new CupoSuperadoException("El cliente no puede alquilar " . count($numerosProductos) . " productos. Superaría su cupo máximo de 2");
                }
                
                // Si pasa todas las verificaciones, añadir a la lista de productos a alquilar
                $productosAAlquilar[] = $producto;
            }

            // Si todos los productos están disponibles, procedemos con el alquiler de todos los productos
            foreach ($productosAAlquilar as $producto) {
                // Realizar el alquiler individual de cada producto
                $cliente->alquilar($producto);
                
                // Actualizar estadísticas del videoclub
                $this->numProductosAlquilados++;    // Incrementar contador de productos alquilados actualmente
                $this->numTotalAlquileres++;        // Incrementar contador histórico de alquileres
            }
            
            // Confirmación de éxito
            echo "<br><strong>Alquiler múltiple completado:</strong> " . count($numerosProductos) . " productos alquilados a {$cliente->nombre}<br>";
            
        } catch (VideoclubException $e) {
            // Si ocurre cualquier error durante la verificación o ejecución, se cancela toda la operación
            echo "<br><strong>Error en alquiler múltiple:</strong> " . $e->getMessage() . "<br>";
            echo "<em>Ningún producto ha sido alquilado debido al error.</em><br>";
        }

        // Retornar $this para permitir encadenamiento de métodos
        return $this;
    }

    // Este método permite que un socio devuelva un producto alquilado por su número
    public function devolverSocioProducto(int $numSocio, int $numeroProducto): Videoclub
    {
        try {
            $cliente = $this->buscarSocio($numSocio);
            
            // Verificar que el cliente existe
            if (!$cliente) {
                throw new ClienteNoEncontradoException("Socio con número $numSocio no encontrado");
            }

            // El método devolver del Cliente ya maneja la lógica interna
            // y actualiza el estado del soporte automáticamente
            $cliente->devolver($numeroProducto);
            
            // Actualizar estadísticas del videoclub: reducir productos alquilados
            $this->numProductosAlquilados--;
            
        } catch (ClienteNoEncontradoException $e) {
            echo "<br><strong>Error en devolución:</strong> " . $e->getMessage() . "<br>";
        }

        return $this;
    }

    // Este método permite que un socio devuelva múltiples productos alquilados en una sola operación
    public function devolverSocioProductos(int $numSocio, array $numerosProductos): Videoclub
    {
        try {
            $cliente = $this->buscarSocio($numSocio);
            
            // Verificar que el cliente existe
            if (!$cliente) {
                throw new ClienteNoEncontradoException("Socio con número $numSocio no encontrado");
            }

            $devolucionesExitosas = 0;
            
            // Devolver cada producto individualmente
            foreach ($numerosProductos as $numeroProducto) {
                try {
                    $cliente->devolver($numeroProducto);
                    $devolucionesExitosas++;
                    $this->numProductosAlquilados--; // Actualizar estadística por cada devolución
                } catch (\Exception $e) {
                    // Si falla una devolución, continuamos con las siguientes
                    // El método devolver ya muestra mensajes de error internos
                    continue;
                }
            }
            
            // Confirmación de éxito
            if ($devolucionesExitosas > 0) {
                echo "<br><strong>Devolución múltiple completada:</strong> $devolucionesExitosas productos devueltos por {$cliente->nombre}<br>";
            }
            
        } catch (ClienteNoEncontradoException $e) {
            echo "<br><strong>Error en devolución múltiple:</strong> " . $e->getMessage() . "<br>";
        }

        return $this;
    }

    // Muestra estadísticas del videoclub
    public function mostrarEstadisticas(): void
    {
        echo "<br><strong> Estadísticas del Videoclub \"{$this->nombre}\":</strong><br>";
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