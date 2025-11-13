<?php
session_start();

// Si el usuario no está logueado, lo mandamos al login
if (!isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit();
}

$nombre_usuario = $_SESSION['usuario'];

// Solo el admin puede ver esta página
if ($nombre_usuario !== 'admin') {
    header('Location: main.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Videoclub - Panel Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        h1 {
            color: #333;
        }

        h2 {
            color: #666;
            margin-top: 30px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 30px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .disponible {
            color: green;
            font-weight: bold;
        }

        .no-disponible {
            color: red;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <h1>Panel de Administración - Videoclub</h1>

    <p>Hola, <strong><?php echo $nombre_usuario; ?></strong> (Administrador)</p>

    <p><a href="logout.php">Cerrar Sesión</a></p>

    <hr>

    <!-- LISTADO DE CLIENTES -->
    <h2>Listado de Clientes</h2>

    <?php if (isset($_SESSION['clientes']) && !empty($_SESSION['clientes'])): ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Teléfono</th>
            </tr>
            <?php foreach ($_SESSION['clientes'] as $cliente): ?>
                <tr>
                    <td><?php echo $cliente['id']; ?></td>
                    <td><?php echo $cliente['nombre']; ?></td>
                    <td><?php echo $cliente['email']; ?></td>
                    <td><?php echo $cliente['telefono']; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No hay clientes registrados.</p>
    <?php endif; ?>

    <!-- LISTADO DE SOPORTES -->
    <h2>Listado de Soportes</h2>

    <?php if (isset($_SESSION['soportes']) && !empty($_SESSION['soportes'])): ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Tipo</th>
                <th>Precio</th>
                <th>Disponible</th>
            </tr>
            <?php foreach ($_SESSION['soportes'] as $soporte): ?>
                <tr>
                    <td><?php echo $soporte['id']; ?></td>
                    <td><?php echo $soporte['titulo']; ?></td>
                    <td><?php echo $soporte['tipo']; ?></td>
                    <td><?php echo $soporte['precio']; ?>€</td>
                    <td>
                        <?php if ($soporte['disponible']): ?>
                            <span class="disponible">SÍ</span>
                        <?php else: ?>
                            <span class="no-disponible">NO</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No hay soportes en el catálogo.</p>
    <?php endif; ?>

</body>

</html>