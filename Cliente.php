<?php

// Incluimos la clase padre con require_once para evitar redefiniciones
require_once "Soporte.php";

class Cliente
{
    // ATRIBUTOS

    public $nombre;
    public $numero;
    private $soportesAlquilados = []; // Array de objetos Soporte
    private $numSoportesAlquilados = 0;
    private $maxAlquilerConcurrente;

    // CONSTRUCTOR
    public function __construct($nombre, $numero, $maxAlquilerConcurrente = 3)
    {
        $this->nombre = $nombre;
        $this->numero = $numero;
        $this->maxAlquilerConcurrente = $maxAlquilerConcurrente;
    }

    // GETTERS
    public function getNumero()
    {
        return $this->numero;
    }

    public function getNumSoportesAlquilados()
    {
        return $this->numSoportesAlquilados;
    }

    // SETTERS
    public function setNumero($numero)
    {
        $this->numero = $numero;
    }

    // MÉTODOS DE GESTIÓN DE ALQUILERES

    // Este método comprueba si el cliente ya tiene alquilado un soporte
    public function tieneAlquilado(Soporte $s): bool
    {
        // Miramos todos los soportes que tiene alquilados
        foreach ($this->soportesAlquilados as $soporteAlquilado) {
            // Comparamos los números de soporte para ver si es el mismo
            if ($soporteAlquilado->getNumero() == $s->getNumero()) {
                return true;
            }
        }
        return false;
    }

    public function alquilar(Soporte $s): bool
    {
        if ($this->tieneAlquilado($s)) {
            echo "<br>El cliente ya tiene alquilado el soporte " . $s->titulo . "<br>";
            return false;
        }

        if ($this->numSoportesAlquilados >= $this->maxAlquilerConcurrente) {
            echo "<br>Este cliente tiene " . $this->maxAlquilerConcurrente . " elementos alquilados. No puede alquilar más en este videoclub hasta que no devuelva algo<br>";
            return false;
        }

        $this->soportesAlquilados[] = $s;
        $this->numSoportesAlquilados++;

        echo "<br>Alquilado soporte a: " . $this->nombre . "<br>";
        $s->muestraResumen(); // ← Aquí se muestra la información del soporte

        return true;
    }

    // Este método permite devolver un soporte alquilado por su número
    public function devolver(int $numSoporte): bool
    {
        // Recorremos el array de soportes alquilados
        foreach ($this->soportesAlquilados as $indice => $soporte) {
            // Si encontramos el soporte con el número indicado
            if ($soporte->getNumero() == $numSoporte) {
                // Lo eliminamos del array
                unset($this->soportesAlquilados[$indice]);

                // Reindexamos el array para que no haya huecos
                $this->soportesAlquilados = array_values($this->soportesAlquilados);

                // Restamos 1 al contador de alquileres
                $this->numSoportesAlquilados--;

                echo "<br>" . $this->nombre . " ha devuelto correctamente el soporte: " . $soporte->titulo . "<br>";
                return true;
            }
        }

        // Si no se encuentra el soporte en la lista de alquileres
        echo "<br>" . $this->nombre . " no tiene alquilado el soporte con número: " . $numSoporte . "<br>";
        return false;
    }

    // Este método muestra la lista de alquileres actuales del cliente
    public function listarAlquileres(): void
    {
        echo "<br><strong>" . $this->nombre . " tiene " . $this->numSoportesAlquilados . " soporte(s) alquilado(s):</strong><br>";

        // Si tiene soportes alquilados, los mostramos
        if ($this->numSoportesAlquilados > 0) {
            foreach ($this->soportesAlquilados as $soporte) {
                echo " - " . $soporte->titulo . " (Nº: " . $soporte->getNumero() . ")<br>";
            }
        } else {
            // Si no tiene ninguno
            echo "No hay soportes alquilados actualmente.<br>";
        }
    }

    // MÉTODOS DE VISUALIZACIÓN

    // Este método muestra toda la información del cliente en pantalla
    public function muestraResumen()
    {
        echo "<strong>Nombre:</strong> " . $this->nombre . "<br>";

        echo "<strong>Número de cliente:</strong> " . $this->numero . "<br>";

        echo "<strong>Cantidad de alquileres:</strong> " . $this->numSoportesAlquilados . "<br>";

        // Si tiene algún soporte alquilado, los muestra
        if ($this->numSoportesAlquilados > 0) {
            echo "<strong>Soportes alquilados:</strong><br>";

            // Recorremos todos los soportes alquilados
            foreach ($this->soportesAlquilados as $soporte) {
                echo " - " . $soporte->titulo . " (Nº: " . $soporte->getNumero() . ")<br>";
            }
        } else {
            // Si no tiene ningún soporte alquilado
            echo "<strong>Soportes alquilados:</strong> Ninguno<br>";
        }
    }
}
