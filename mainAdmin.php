<?php
// Iniciamos sesión siendo admin
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit();
}

$nombre_usuario = $_SESSION['usuario'];

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
    <link rel="stylesheet" href="css/css.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #333; }
        h2 { color: #666; margin-top: 30px; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 30px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .disponible { color: green; font-weight: bold; }
        .no-disponible { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <div class="vc-admin-container">
        <div class="vc-header justify-content-between">
            <div>
                <h1>Panel de Administración - Videoclub</h1>
                <div class="vc-subtitle">Gestión completa del sistema</div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="vc-highlight">Hola, <?php echo $nombre_usuario; ?> (Administrador)</span>
                <a href="logout.php" class="btn btn-outline-danger btn-sm btn-return">Cerrar Sesión</a>
            </div>
        </div>
        
        <a href="formCreateCliente.php" class="btn btn-primary btn-sm mb-3">Añadir Nuevo Cliente</a>

        <!-- LISTADO DE CLIENTES -->
        <div class="vc-admin-section">
            <h2>Listado de Clientes</h2>
            
            <?php if (isset($_SESSION['clientes']) && !empty($_SESSION['clientes'])): ?>
                <table class="vc-admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Usuario</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Aquí imprimimos los datos del usuario los cuales están guardados -->
                        <?php foreach ($_SESSION['clientes'] as $cliente): ?>
                        <tr>
                            <td><?php echo $cliente['id']; ?></td>
                            <td><?php echo $cliente['nombre']; ?></td>
                            <td><strong><?php echo $cliente['user']; ?></strong></td>
                            <td><?php echo $cliente['email']; ?></td>
                            <td><?php echo $cliente['telefono']; ?></td>
                            <td>
                                <a href="formUpdateCliente.php?id=<?php echo $cliente['id']; ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                                <a href="removeCliente.php?id=<?php echo $cliente['id']; ?>" 
                                class="btn btn-sm btn-outline-danger" 
                                onclick="return confirm('¿Estás seguro de que quieres eliminar este cliente?');">
                                    Eliminar
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="vc-empty">No hay clientes registrados.</p>
            <?php endif; ?>
        </div>
        
        <!-- LISTADO DE SOPORTES -->
        <div class="vc-admin-section">
            <h2>Listado de Soportes</h2>
            
            <?php if (isset($_SESSION['soportes']) && !empty($_SESSION['soportes'])): ?>
                <table class="vc-admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Tipo</th>
                            <th>Precio</th>
                            <th>Disponible</th>
                        </tr>
                    </thead>
                    <tbody>
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
                    </tbody>
                </table>
            <?php else: ?>
                <p class="vc-empty">No hay soportes en el catálogo.</p>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>