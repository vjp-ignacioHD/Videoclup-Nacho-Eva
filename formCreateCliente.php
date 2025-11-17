<?php
session_start();

// Requiere estar logueado como admin
if (!isset($_SESSION['usuario']) || $_SESSION['usuario'] !== 'admin') {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Añadir Nuevo Cliente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Añadir Nuevo Cliente</h2>

        <?php if (isset($_SESSION['error_create'])): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($_SESSION['error_create']); ?>
                <?php unset($_SESSION['error_create']); ?>
            </div>
        <?php endif; ?>

        <form action="createCliente.php" method="POST">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre completo</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>
            <div class="mb-3">
                <label for="user" class="form-label">Usuario (único)</label>
                <input type="text" class="form-control" id="user" name="user" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="telefono" class="form-label">Teléfono</label>
                <input type="tel" class="form-control" id="telefono" name="telefono" required>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-success">Crear Cliente</button>
                <a href="mainAdmin.php" class="btn btn-secondary">Volver al Panel</a>
            </div>
        </form>
    </div>
</body>
</html>