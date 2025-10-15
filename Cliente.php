<?php

// Incluimos la clase padre con require_once para evitar redefiniciones
require_once "Soporte.php";

class Cliente {
    // Atributos
    public $nombre;
    public $numero;
    private $soportesAlquilados = [];  // Array de objetos Soporte
    private $numSoportesAlquilados = 0;
    private $maxAlquilerConcurrente;

    // Constructor
    public function __construct($nombre, $numero, $maxAlquilerConcurrente = 3) {
        $this->nombre = $nombre;
        $this->numero = $numero;
        $this->maxAlquilerConcurrente = $maxAlquilerConcurrente;
    }

    // Getters
    public function getNumero() {
        return $this->numero;
    }

    public function getNumSoportesAlquilados() {
        return $this->numSoportesAlquilados;
    }

    // Setter
    public function setNumero($numero) {
        $this->numero = $numero;
    }


    // Método para mostrar el resumn
    public function muestraResumen() {
        echo "<strong>Nombre:</strong> " . $this->nombre . "<br>";
        echo "<strong>Cantidad de alquileres:</strong> " . $this->numSoportesAlquilados . "<br>";
    }
}

?>