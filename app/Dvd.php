<?php

namespace App;

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

    public function getPuntuacion(): ?float
{
    if (empty($this->metacritic)) {
        return null;
    }

    // Obtiene la puntuación de Metacritic para este DVD.
    try {
        $html = file_get_contents($this->metacritic);
        if ($html === false) {
            return null;
        }

        // Buscar la puntuación en el HTML de Metacritic
        // Ejemplo: <span class="score_summary">Metascore</span><div class="metascore_w large movie positive">84</div>
        preg_match('/<div class="metascore_w large[^"]*">(\d+)<\/div>/', $html, $matches);

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