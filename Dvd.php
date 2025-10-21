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
    public function muestraResumen(): void
    {
        // Mostramos los datos heredados manualmente
        echo "Título: " . htmlspecialchars($this->titulo) . "<br>";
        echo "Número: " . $this->numero . "<br>";
        echo "Precio: " . number_format($this->getPrecio(), 2) . "€<br>";
        
        // Datos específicos del DVD
        echo "<strong>Idiomas:</strong> " . htmlspecialchars($this->idiomas) . "<br>";
        echo "<strong>Formato de pantalla:</strong> " . htmlspecialchars($this->formatoPantalla) . "<br>";
    }
}

?>