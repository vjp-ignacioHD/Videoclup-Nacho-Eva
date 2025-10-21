<?php

namespace Dwes\ProyectoVideoclub;

// IMPORTANTE: Incluimos la clase padre antes de heredar de ella (Contenido corregido por IA)
include_once "Soporte.php";

class CintaVideo extends Soporte {
    // Atributos
    private $duracion;

    // Constructor
    public function __construct($titulo, $numero, $precio, $duracion) { 
        parent::__construct($titulo, $numero, $precio); // Llamamos al constructor del padre
        $this->duracion = $duracion;
    }

    // Métodos
    public function muestraResumen(): void
    {
        // Mostramos manualmente los atributos heredados de Soporte
        echo "Título: " . htmlspecialchars($this->titulo) . "<br>";
        echo "Número: " . $this->numero . "<br>";
        echo "Precio: " . number_format($this->getPrecio(), 2) . "€<br>";
        
        // Luego añadimos la información específica de la cinta
        echo "<strong>Duración:</strong> " . $this->duracion . " minutos<br>";
    }
}

?>