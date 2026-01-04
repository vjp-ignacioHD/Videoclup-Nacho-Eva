<?php
namespace App;

class CintaVideo extends Soporte {
    // Atributos
    private $duracion;

    // Constructor
    public function __construct($titulo, $numero, $precio, $duracion) { 
        parent::__construct($titulo, $numero, $precio); // Llamamos al constructor del padre
        $this->duracion = $duracion;
    }

    // Métodos
    public function muestraResumen(): string
    {
        $resumen = "Título: " . htmlspecialchars($this->titulo) . "<br>";
        $resumen .= "Número: " . $this->numero . "<br>";
        $resumen .= "Precio: " . number_format($this->getPrecio(), 2) . "€<br>";
        $resumen .= "<strong>Duración:</strong> " . $this->duracion . " minutos<br>";

        echo $resumen;
        return $resumen;
    }

    // En Juego.php
    public function getPuntuacion(): ?float
    {
        // Si no hay URL de Metacritic, no se puede obtener la puntuación
        if (empty($this->metacritic)) {
            return null;
        }

        try {
            // Configurar contexto con User-Agent para evitar bloqueos
            $context = stream_context_create([
                "http" => [
                    "header" => "User-Agent: Mozilla/5.0 (compatible; VideoclubBot/1.0)\r\n"
                ]
            ]);

            // Obtener el HTML de la página de Metacritic
            $html = file_get_contents($this->metacritic, false, $context);

            if ($html === false) {
                return null;
            }

            // Buscar la puntuación en el HTML
            // Patrón: <div class="metascore_w large movie positive">84</div>
            if (preg_match('/<div class="metascore_w large movie[^"]*">(\d+)<\/div>/', $html, $matches)) {
                return (float) $matches[1];
            }

            return null;

        } catch (\Exception $e) {
            // Si hay cualquier error (red, timeout, etc.), devolver null
            return null;
        }
    }
}

?>