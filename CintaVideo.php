<?php

// IMPORTANTE: Incluimos la clase padre antes de heredar de ella (Contenido corregido por IA)
require_once "Soporte.php";

class CintaVideo extends Soporte {
    // Atributos
    private $duracion;

    // Constructor
    public function __construct($titulo, $numero, $precio, $duracion) { 
        parent::__construct($titulo, $numero, $precio); // Llamamos al constructor del padre
        $this->duracion = $duracion;
    }

    // Métodos
    public function muestraResumen() {
        parent::muestraResumen(); // Primero mostramos lo del padre
        // Luego añadimos la duración
        echo "<strong>Duración:</strong> " . $this->duracion . " minutos<br>";
    }
}

?>