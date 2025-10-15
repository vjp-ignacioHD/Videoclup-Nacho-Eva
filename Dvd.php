<?php

// Incluimos la clase padre para que funcione la herencia
require_once "Soporte.php";

class Dvd extends Soporte {
    // Atributos
    public $idiomas;
    private $formatoPantalla;

    // Constructor
    public function __construct($titulo, $numero, $precio, $idiomas, $formatoPantalla) {  
        parent::__construct($titulo, $numero, $precio); // Llamamos al constructor del padre
        $this->idiomas = $idiomas;
        $this->formatoPantalla = $formatoPantalla;
    }

    // Métodos
    public function muestraResumen() {
        parent::muestraResumen(); // Primero mostramos lo del padre     
        // Luego añadimos los nuevos datos
        echo "<strong>Idiomas:</strong> " . $this->idiomas . "<br>";
        echo "<strong>Formato de pantalla:</strong> " . $this->formatoPantalla . "<br>";
    }
}

?>