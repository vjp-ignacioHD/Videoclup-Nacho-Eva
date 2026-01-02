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

// Alquilar productos a los socios
$vc->alquilaSocioProducto(1, 0); // Nacho alquila Ghostbusters
$vc->alquilaSocioProducto(2, 1); // Eva alquila Indiana Jones

// Mostrar resúmenes
echo "<h2>Resumen de socios:</h2>";
$vc->listarSocios();

echo "<h2>Resumen de productos:</h2>";
$vc->listarProductos();

echo "<h2>Estadísticas:</h2>";
$vc->mostrarEstadisticas();