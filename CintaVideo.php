<?php

// IMPORTANTE: Incluimos la clase padre antes de heredar de ella (Contenido corregido por IA)
include "Soporte.php";

class CintaVideo extends Soporte {
    // Atributos
    private $duracion;

    // Constructor
    public function __construct($titulo, $numero, $precio, $duracion) {
        // Llamamos al constructor del padre
        parent::__construct($titulo, $numero, $precio);
        $this->duracion = $duracion;
    }

    // Método sobrescrito: muestraResumen()
    public function muestraResumen() {
        // Primero mostramos lo del padre
        parent::muestraResumen();
        // Luego añadimos la duración
        echo "<strong>Duración:</strong> " . $this->duracion . " minutos<br>";
    }
}

?>