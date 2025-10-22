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
    public $alquilado;

    // Constructor
    public function __construct($titulo, $numero, $precio)
    {
        $this->titulo = $titulo;
        $this->numero = $numero;
        $this->precio = $precio;
        $this->alquilado = false; // Inicialmente no está alquilado
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

    public function getAlquilado(): bool
    {
        return $this->alquilado;
    }

    // Setter
    public function setAlquilado(bool $estado): void
    {
        $this->alquilado = $estado;
    }

    // Este método debe ser implementado por cada clase hija
    abstract public function muestraResumen();
}
