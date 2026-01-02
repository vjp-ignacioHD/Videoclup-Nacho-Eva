<?php

namespace App;

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
    public function muestraResumen(): void
    {
        // Mostramos los datos heredados manualmente
        echo "Título: " . htmlspecialchars($this->titulo) . "<br>";
        echo "Número: " . $this->numero . "<br>";
        echo "Precio: " . number_format($this->getPrecio(), 2) . "€<br>";
        
        // Datos específicos del juego
        echo "<strong>Consola:</strong> " . htmlspecialchars($this->consola) . "<br>";
        echo "<strong>Jugadores posibles:</strong> ";
        $this->muestraJugadoresPosibles();
        echo "<br>";
    }

    // En Juego.php
    public function getPuntuacion(): ?float
    {
        if (empty($this->metacritic)) {
            return null;
        }

        try {
            $html = file_get_contents($this->metacritic);
            if ($html === false) {
                return null;
            }

            preg_match('/<div class="metascore_w large game[^"]*">(\d+)<\/div>/', $html, $matches);

            if (isset($matches[1])) {
                return (float) $matches[1];
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
}

?>