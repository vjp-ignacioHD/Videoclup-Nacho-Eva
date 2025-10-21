<?php

// Incluimos el autoloader
require_once "../autoload.php";

// Usamos use para importar las clases que necesitamos
use Dwes\ProyectoVideoclub\Videoclub;
use Dwes\ProyectoVideoclub\Cliente;
use Dwes\ProyectoVideoclub\Juego;
use Dwes\ProyectoVideoclub\Dvd;
use Dwes\ProyectoVideoclub\CintaVideo;
use Dwes\ProyectoVideoclub\Soporte;

// Creamos el videoclub
$videoclub = new Videoclub("CineClub Express");

echo "¡Videoclub creado correctamente!<br>";

// Añadimos socios y productos (uno por uno)
$videoclub->incluirSocio("Ana", 0);
$videoclub->incluirSocio("Luis", 1);
$videoclub->incluirJuego("The Legend of Zelda", 39.99, "Nintendo Switch", 1, 2);
$videoclub->incluirDvd("Interestelar", 14.99, "Español, Inglés", "Widescreen");
$videoclub->incluirCintaVideo("Titanic", 9.99, 194);

// Mostramos los productos
$videoclub->listarProductos();

// Alquilamos productos (uno por uno)
$videoclub->alquilaSocioProducto(1, 0); // Ana alquila Zelda
$videoclub->alquilaSocioProducto(1, 1); // Ana alquila Interestelar
$videoclub->alquilaSocioProducto(2, 2); // Luis alquila Titanic

// Mostramos socios
$videoclub->listarSocios();

// Devoluciones (sin encadenar)
$ana = $videoclub->buscarSocio(1);
if ($ana) {
    $ana->devolver(0); // Devuelve Zelda
    $ana->devolver(1); // Devuelve Interestelar
}

// Estado final
echo "<hr><h3>Estado final:</h3>";
$videoclub->listarSocios();