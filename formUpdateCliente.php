<?php
session_start();

// Solo accesible para admin o el propio cliente
if (!isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit();
}

$usuario_actual = $_SESSION['usuario'];
$es_admin = ($usuario_actual === 'admin');

// Obtener ID del cliente a editar
$id = $_GET['id'] ?? null;
if ($id === null) {
    header('Location: mainAdmin.php');
    exit();
}

// Buscar cliente por ID
$cliente = null;
foreach ($_SESSION['clientes'] ?? [] as $c) {
    if ($c['id'] == $id) {
        $cliente = $c;
        break;
    }
}
if (!$cliente) {
    header('Location: mainAdmin.php');
    exit();
}

// Si es cliente normal, solo puede editarse a sí mismo
if (!$es_admin && $cliente['user'] !== $usuario_actual) {
    header('Location: mainCliente.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Editar Cliente: <?php echo htmlspecialchars($cliente['nombre']); ?></h2>

        <?php if (isset($_SESSION['error_update'])): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($_SESSION['error_update']); ?>
                <?php unset($_SESSION['error_update']); ?>
            </div>
        <?php endif; ?>

        <form action="updateCliente.php" method="POST">
            <input type="hidden" name="id" value="<?php echo $cliente['id']; ?>">

            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre completo</label>
                <input type="text" class="form-control" id="nombre" name="nombre" 
                       value="<?php echo htmlspecialchars($cliente['nombre']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="user" class="form-label">Usuario</label>
                <input type="text" class="form-control" id="user" name="user" 
                       value="<?php echo htmlspecialchars($cliente['user']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" 
                       value="<?php echo htmlspecialchars($cliente['email']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="telefono" class="form-label">Teléfono</label>
                <input type="tel" class="form-control" id="telefono" name="telefono" 
                       value="<?php echo htmlspecialchars($cliente['telefono']); ?>" required>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                <?php if ($es_admin): ?>
                    <a href="mainAdmin.php" class="btn btn-secondary">Volver al Panel</a>
                <?php else: ?>
                    <a href="mainCliente.php" class="btn btn-secondary">Volver a mi perfil</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</body>
</html>