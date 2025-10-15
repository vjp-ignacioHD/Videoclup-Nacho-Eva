<?php

// Incluimos la clase padre con require_once para evitar redefiniciones
require_once "Soporte.php";

class Juego extends Soporte {
    // Atributos
    public $consola;
    private $minNumJugadores;
    private $maxNumJugadores;

    // Constructor
    public function __construct($titulo, $numero, $precio, $consola, $minNumJugadores, $maxNumJugadores) {
        parent::__construct($titulo, $numero, $precio);
        $this->consola = $consola;
        $this->minNumJugadores = $minNumJugadores;
        $this->maxNumJugadores = $maxNumJugadores;
    }

    // Métodos
    // Metodo para mostrar los jugadores posibles
    public function muestraJugadoresPosibles() {
        if ($this->minNumJugadores == $this->maxNumJugadores) {
            if ($this->minNumJugadores == 1) {
                echo "Para un jugador";
            } else {
                echo "Para " . $this->minNumJugadores . " jugadores";
            }
        } else {
            echo "De " . $this->minNumJugadores . " a " . $this->maxNumJugadores . " jugadores";
        }
    }

    // Metodo para mostrar el resumen
    public function muestraResumen() {
        parent::muestraResumen();
        echo "<strong>Consola:</strong> " . $this->consola . "<br>";
        echo "<strong>Jugadores posibles:</strong> ";
        $this->muestraJugadoresPosibles();
        echo "<br>";
    }
}

?>