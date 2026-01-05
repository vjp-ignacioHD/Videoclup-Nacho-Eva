<?php
namespace App;

class Bluray extends Soporte {
    private $duracion;
    private $es4k;

    public function __construct($titulo, $numero, $precio, $duracion, $es4k) {  
        parent::__construct($titulo, $numero, $precio);
        $this->duracion = $duracion;
        $this->es4k = $es4k;
    }

    // Getters
    public function getDuracion(): int {
        return $this->duracion;
    }

    public function es4k(): bool {
        return $this->es4k;
    }

    public function muestraResumen(): string
    {
        $resumen = "Título: " . htmlspecialchars($this->titulo) . "<br>";
        $resumen .= "Número: " . $this->numero . "<br>";
        $resumen .= "Precio: " . number_format($this->getPrecio(), 2) . "€<br>";
        $resumen .= "<strong>Duración:</strong> " . $this->duracion . " minutos<br>";
        $resumen .= "<strong>4K:</strong> " . ($this->es4k ? "Sí" : "No") . "<br>";
        
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

            // Usamos el mismo patrón que CintaVideo para películas
            if (preg_match('/<div class="metascore_w large movie[^"]*">(\d+)<\/div>/', $html, $matches)) {
                return (float) $matches[1];
            }

            return null;

        } catch (\Exception $e) {
            return null;
        }
    }
}
?>