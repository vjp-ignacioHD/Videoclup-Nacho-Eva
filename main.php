<?php
session_start();

// Si el usuario no está logueado, lo mandamos al login
if (!isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit();
}

$nombre_usuario = $_SESSION['usuario'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Videoclub - Principal</title>
</head>
<body>
    <h1>Bienvenido al Videoclub</h1>
    
    <p>Hola, <strong><?php echo $nombre_usuario; ?></strong></p>
    
    <p><a href="logout.php">Cerrar Sesión</a></p>
</body>
</html>