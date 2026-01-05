<?php

namespace App;

class Dvd extends Soporte
{
    public $idiomas;
    private $formatoPantalla;
    private $duracion; // Nueva propiedad

    public function __construct($titulo, $numero, $precio, $idiomas, $formatoPantalla, $duracion)
    {
        parent::__construct($titulo, $numero, $precio);
        $this->idiomas = $idiomas;
        $this->formatoPantalla = $formatoPantalla;
        $this->duracion = $duracion;
    }

    // Getter para duración
    public function getDuracion(): int
    {
        return $this->duracion;
    }

    public function muestraResumen(): string
    {
        $resumen = "Título: " . htmlspecialchars($this->titulo) . "<br>";
        $resumen .= "Número: " . $this->numero . "<br>";
        $resumen .= "Precio: " . number_format($this->getPrecio(), 2) . "€<br>";
        $resumen .= "<strong>Duración:</strong> " . $this->duracion . " minutos<br>";
        $resumen .= "<strong>Idiomas:</strong> " . htmlspecialchars($this->idiomas) . "<br>";  // Sin espacio
        $resumen .= "<strong>Formato de pantalla:</strong> " . htmlspecialchars($this->formatoPantalla) . "<br>";

        echo $resumen;
        return $resumen;
    }

    // Y añadir un método para mostrar (opcional)
    public function mostrarResumen(): void
    {
        echo $this->muestraResumen();
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
