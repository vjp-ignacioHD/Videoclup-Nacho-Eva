<?php
namespace App;

class Dvd extends Soporte {
    public $idiomas;
    private $formatoPantalla;

    public function __construct($titulo, $numero, $precio, $idiomas, $formatoPantalla) {  
        parent::__construct($titulo, $numero, $precio);
        $this->idiomas = $idiomas;
        $this->formatoPantalla = $formatoPantalla;
    }

    public function muestraResumen(): string
    {
        $resumen = "Título: " . htmlspecialchars($this->titulo) . "<br>";
        $resumen .= "Número: " . $this->numero . "<br>";
        $resumen .= "Precio: " . number_format($this->getPrecio(), 2) . "€<br>";
        $resumen .= "<strong>Idiomas:</strong> " . htmlspecialchars($this->idiomas) . "<br>";
        $resumen .= "<strong>Formato de pantalla:</strong> " . htmlspecialchars($this->formatoPantalla) . "<br>";
        
        echo $resumen;
        return $resumen;
    }

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