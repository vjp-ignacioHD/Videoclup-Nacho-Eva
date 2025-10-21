<?php

namespace Dwes\ProyectoVideoclub;

abstract class Soporte implements Resumible
{

    // Constante 
    private static $IVA = 0.21;

    // Atributos 
    public $titulo;
    protected $numero;
    private $precio;

    // Constructor
    public function __construct($titulo, $numero, $precio)
    {
        $this->titulo = $titulo;
        $this->numero = $numero;
        $this->precio = $precio;
    }

    // Getters
    public function getPrecio()
    {
        return $this->precio;
    }

    public function getPrecioConIva()
    {
        return $this->precio * (1 + self::$IVA);
    }

    public function getNumero()
    {
        return $this->numero;
    }

    // Este método debe ser implementado por cada clase hija
    abstract public function muestraResumen();
}
