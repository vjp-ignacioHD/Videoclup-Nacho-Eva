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

// Añadimos socios y productos
$videoclub->incluirSocio("Ana", 0);
$videoclub->incluirSocio("Luis", 1);
$videoclub->incluirJuego("The Legend of Zelda", 39.99, "Nintendo Switch", 1, 2);
$videoclub->incluirDvd("Interestelar", 14.99, "Español, Inglés", "Widescreen");
$videoclub->incluirCintaVideo("Titanic", 9.99, 194);

// Alquilamos productos
$videoclub->alquilaSocioProducto(1, 0); // Ana alquila Zelda
$videoclub->alquilaSocioProducto(1, 1); // Ana alquila Interestelar
$videoclub->alquilaSocioProducto(2, 2); // Luis alquila Titanic

// Devoluciones
$ana = $videoclub->buscarSocio(1);
if ($ana) {
    $ana->devolver(0); // Devuelve Zelda
    $ana->devolver(1); // Devuelve Interestelar
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CineClub Express</title>

    <!-- Bootstrap + tu CSS personalizado -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/videoclub-bootstrap.css">
</head>
<body>
    <div class="container vc-container py-4">
        
        <!-- Cabecera elegante -->
        <header class="vc-header shadow-sm mb-4 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="mb-0">🎬 Videoclub</h1>
                
            </div>
        </header>

        <!-- Mensaje de éxito -->
        <div class="alert alert-success shadow-sm text-center">
            <strong>¡Videoclub creado correctamente!</strong> Los datos se han inicializado sin errores.
        </div>

        <!-- Productos -->
        <section class="mb-5">
            <div class="d-flex align-items-center mb-3">
                <h3 class="me-2 mb-0">📀 Catálogo de productos</h3>
                <hr class="flex-grow-1">
            </div>

            <div class="vc-products-grid">
                <?php $videoclub->listarProductos(); ?>
            </div>
        </section>

        <!-- Socios -->
        <section class="mb-5">
            <div class="d-flex align-items-center mb-3">
                <h3 class="me-2 mb-0">👥 Socios del videoclub</h3>
                <hr class="flex-grow-1">
            </div>

            <div class="table-responsive shadow-sm rounded">
                <?php $videoclub->listarSocios(); ?>
            </div>
        </section>

        <!-- Estado final -->
        <section>
            <div class="d-flex align-items-center mb-3">
                <h3 class="me-2 mb-0">🏁 Estado final</h3>
                <hr class="flex-grow-1">
            </div>
            <?php $videoclub->listarSocios(); ?>
        </section>

        <!-- Footer -->
        <footer class="vc-footer mt-5 border-top pt-3">
            <p class="mb-0">
                &copy; <?= date("Y"); ?> <span class="fw-semibold text-primary">Videoclub</span> — Proyecto PHP para gestión de videoclub.<br>
                <small class="text-muted">Desarrollado por Nacho y Eva.</small>
            </p>
        </footer>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
