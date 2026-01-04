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
        $mensaje = "";
        if ($this->minNumJugadores == $this->maxNumJugadores) {
            if ($this->minNumJugadores == 1) {
                $mensaje = "Para un jugador";
            } else {
                $mensaje = "Para " . $this->minNumJugadores . " jugadores";
            }
        } else {
            $mensaje = "De " . $this->minNumJugadores . " a " . $this->maxNumJugadores . " jugadores";
        }
        
        echo $mensaje;
        return $mensaje;
    }

    // Metodo para mostrar el resumen
    public function muestraResumen(): string
    {
        $resumen = "Título: " . htmlspecialchars($this->titulo) . "<br>";
        $resumen .= "Número: " . $this->numero . "<br>";
        $resumen .= "Precio: " . number_format($this->getPrecio(), 2) . "€<br>";
        $resumen .= "<strong>Consola:</strong> " . htmlspecialchars($this->consola) . "<br>";
        $resumen .= "<strong>Jugadores posibles:</strong> ";
        
        // Para la parte de jugadores, necesitamos capturar el output
        ob_start();
        $this->muestraJugadoresPosibles();
        $jugadores = ob_get_clean();
        $resumen .= $jugadores . "<br>";
        
        echo $resumen;
        return $resumen;
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