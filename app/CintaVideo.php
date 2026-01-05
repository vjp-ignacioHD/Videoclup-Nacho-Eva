<?php
namespace App;

class CintaVideo extends Soporte {
    private $duracion;

    public function __construct($titulo, $numero, $precio, $duracion) { 
        parent::__construct($titulo, $numero, $precio);
        $this->duracion = $duracion;
    }

    public function muestraResumen(): string
    {
        $resumen = "Título: " . htmlspecialchars($this->titulo) . "<br>";
        $resumen .= "Número: " . $this->numero . "<br>";
        $resumen .= "Precio: " . number_format($this->getPrecio(), 2) . "€<br>";
        $resumen .= "<strong>Duración:</strong> " . $this->duracion . " minutos<br>";
        
        echo $resumen;
        return $resumen;
    }

    public function getPuntuacion(): ?float
    {
        if (empty($this->metacritic)) {
            return null;
        }

        try {
            $context = stream_context_create([
                "http" => [
                    "header" => "User-Agent: Mozilla/5.0 (compatible; VideoclubBot/1.0)\r\n"
                ]
            ]);

            $html = file_get_contents($this->metacritic, false, $context);

            if ($html === false) {
                return null;
            }

            if (preg_match('/<div class="metascore_w large movie positive">(\d+)<\/div>/', $html, $matches)) {
                return (float) $matches[1];
            }

            return null;

        } catch (\Exception $e) {
            return null;
        }
    }
}
?>