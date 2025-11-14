<?php
session_start();

// 1. Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit();
}

$nombre_usuario = $_SESSION['usuario'];

// 2. Verificar que no sea admin (por si acaso)
if ($nombre_usuario === 'admin') {
    header('Location: mainAdmin.php');
    exit();
}

// 3. Buscar el cliente que coincida con el usuario logueado
$cliente_encontrado = null;

if (isset($_SESSION['clientes'])) {
    foreach ($_SESSION['clientes'] as $cliente_data) {
        // Comparar el campo 'user' con el usuario logueado
        if ($cliente_data['user'] === $nombre_usuario) {
            $cliente_encontrado = $cliente_data;
            break;
        }
    }
}

// 4. Si no encontramos cliente, redirigimos a main.php
if (!$cliente_encontrado) {
    header('Location: main.php');
    exit();
}

// 5. Mostrar la página del cliente
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Videoclub - Cliente <?php echo htmlspecialchars($cliente_encontrado['nombre']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .vc-header {
            background: #f8f9fa;
            padding: 20px;
            border-bottom: 1px solid #dee2e6;
            margin-bottom: 30px;
        }
        .alquiler-item {
            background: #fff;
            border: 1px solid #dee2e6;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="vc-header d-flex justify-content-between align-items-center">
            <h1>Bienvenido, <?php echo htmlspecialchars($cliente_encontrado['nombre']); ?></h1>
            <a href="logout.php" class="btn btn-outline-danger btn-sm">Cerrar Sesión</a>
        </div>

        <h2>Tus Alquileres Actuales</h2>

        <?php
        // Aquí necesitamos simular que tenemos objetos Soporte
        // Pero como solo usamos arrays, haremos una simulación básica

        $alquileres = [];

        // Simulamos que algunos soportes están alquilados por este cliente
        // Por ejemplo: si el cliente es "juanito", le asignamos el soporte "El Padrino"
        if ($cliente_encontrado['user'] === 'juanito') {
            $alquileres[] = [
                'titulo' => 'El Padrino',
                'tipo' => 'DVD',
                'precio' => 15.99,
                'disponible' => false
            ];
        } elseif ($cliente_encontrado['user'] === 'maria') {
            $alquileres[] = [
                'titulo' => 'The Last of Us',
                'tipo' => 'Juego',
                'precio' => 49.99,
                'disponible' => false
            ];
        } elseif ($cliente_encontrado['user'] === 'carlos') {
            $alquileres[] = [
                'titulo' => 'Pulp Fiction',
                'tipo' => 'DVD',
                'precio' => 12.99,
                'disponible' => false
            ];
        }

        if (empty($alquileres)) {
            echo '<p class="text-muted">No tienes ningún soporte alquilado en este momento.</p>';
        } else {
            echo '<div class="row">';
            foreach ($alquileres as $alquiler) {
                echo '<div class="col-md-6 mb-3">';
                echo '<div class="alquiler-item">';
                echo '<h5>' . htmlspecialchars($alquiler['titulo']) . '</h5>';
                echo '<p><strong>Tipo:</strong> ' . htmlspecialchars($alquiler['tipo']) . '</p>';
                echo '<p><strong>Precio:</strong> ' . number_format($alquiler['precio'], 2) . ' €</p>';
                echo '</div>';
                echo '</div>';
            }
            echo '</div>';
        }
        ?>
    </div>
</body>
</html>