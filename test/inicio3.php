<?php
// inicio3.php

require_once 'vendor/autoload.php';

use App\Videoclub;
use App\Cliente;

// Crear el videoclub
$vc = new Videoclub("VideoClub Nacho y Eva");

// Añadir socios
$vc->incluirSocio("Nacho");
$vc->incluirSocio("Eva");

// Añadir productos con sus URLs de Metacritic
$vc->incluirDvd(
    "Ghostbusters", 
    12.99, 
    "Español, Inglés", 
    "16:9", 
    "https://www.metacritic.com/movie/ghostbusters"
);

$vc->incluirDvd(
    "Indiana Jones and the Last Crusade", 
    14.99, 
    "Español, Inglés, Francés", 
    "16:9", 
    "https://www.metacritic.com/movie/indiana-jones-and-the-last-crusade"
);

$vc->incluirJuego(
    "The Legend of Zelda: Breath of the Wild", 
    59.99, 
    "Switch", 
    1, 
    4, 
    "https://www.metacritic.com/game/switch/the-legend-of-zelda-breath-of-the-wild"
);

$vc->incluirJuego(
    "Super Mario Odyssey", 
    59.99, 
    "Switch", 
    1, 
    2, 
    "https://www.metacritic.com/game/switch/super-mario-odyssey"
);

$vc->incluirCintaVideo(
    "Jurassic Park", 
    9.99, 
    127,
    "https://www.metacritic.com/movie/jurassic-park"
);

$vc->incluirCintaVideo(
    "Back to the Future", 
    8.99, 
    116,
    "https://www.metacritic.com/movie/back-to-the-future"
);

// Alquilar productos a un socio (por ejemplo, Nacho, socio 1)
$vc->alquilaSocioProducto(1, 0); // Ghostbusters
$vc->alquilaSocioProducto(1, 2); // Zelda
$vc->alquilaSocioProducto(1, 4); // Jurassic Park

// Obtener alquileres del primer cliente
$cliente = $vc->buscarSocio(1);
if ($cliente) {
    echo "<h2>Alquileres de " . $cliente->nombre . ":</h2>";
    $alquileres = $cliente->getAlquileres();

    if (empty($alquileres)) {
        echo "No tiene soportes alquilados actualmente.<br>";
    } else {
        foreach ($alquileres as $soporte) {
            echo "<strong>Título:</strong> " . htmlspecialchars($soporte->titulo) . "<br>";
            
            // Obtener y mostrar la puntuación de Metacritic
            $puntuacion = $soporte->getPuntuacion();
            if ($puntuacion !== null) {
                echo "<strong>Puntuación Metacritic:</strong> " . $puntuacion . "/100<br><br>";
            } else {
                echo "<strong>Puntuación Metacritic:</strong> No disponible<br><br>";
            }
        }
    }
} else {
    echo "Cliente no encontrado.";
}

// Opcional: Mostrar estadísticas
echo "<h2>Estadísticas del videoclub:</h2>";
$vc->mostrarEstadisticas();