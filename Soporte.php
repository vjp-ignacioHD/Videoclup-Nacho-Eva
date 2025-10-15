<?php

class Soporte {

    // Constantes
    private static $IVA = 0.21;

    // Atributos
    public $titulo;
    protected $numero;
    private $precio;
    
    // Constructor
    public function __construct($titulo, $numero, $precio) {
        $this->titulo = $titulo;
        $this->numero = $numero;
        $this->precio = $precio;
    }

    // Getters
    public function getPrecio() {
        return $this->precio;
    }

    public function getPrecioConIva() {
        return $this->precio * (1 + self::$IVA);
    }

    public function getNumero() {
        return $this->numero;
    }

    public function muestraResumen() {
        echo "<strong>Título:</strong> " . $this->titulo . "<br>";
        echo "<strong>Número:</strong> " . $this->numero . "<br>";
        echo "<strong>Precio sin IVA:</strong> " . $this->getPrecio() . " euros<br>";
        echo "<strong>Precio con IVA:</strong> " . $this->getPrecioConIva() . " euros<br>";
    }

}

?>