<?php

// Incluimos la clase padre con require_once para evitar redefiniciones
require_once "Soporte.php";

class Cliente {
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

    // Este método intenta alquilar un soporte al cliente
    public function alquilar(Soporte $s): bool
    {
        // Primero comprobamos si ya tiene este soporte alquilado
        if ($this->tieneAlquilado($s)) {
            echo "<br>" . $this->nombre . " ya tiene alquilado el soporte: " . $s->titulo . "<br>";
            return false;
        }

        // Luego comprobamos si ha llegado al límite de alquileres
        if ($this->numSoportesAlquilados >= $this->maxAlquilerConcurrente) {
            echo "<br>" . $this->nombre . " ha superado el cupo máximo de " . $this->maxAlquilerConcurrente . " alquileres<br>";
            return false;
        }

        // Añadimos el soporte a su lista de alquilados
        $this->soportesAlquilados[] = $s;
        
        // Sumamos 1 al contador de alquileres
        $this->numSoportesAlquilados++;
        
        echo "<br>" . $this->nombre . " ha alquilado correctamente: " . $s->titulo . "<br>";
        
        return true; 
    }

    // MÉTODOS DE VISUALIZACIÓN
    
    // Este método muestra toda la información del cliente en pantalla
    public function muestraResumen() {
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
?>