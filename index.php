<?php
// Iniciamos la sesión para poder guardar datos del usuario
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Videoclub - Login</title>
    <link rel="stylesheet" href="css/css.css">
</head>
<body>
    <div class="vc-container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="vc-header text-center mb-4">
                    <h1>Videoclub</h1>
                    <div class="vc-subtitle">Sistema de acceso</div>
                </div>
                
                <?php
                // Mostrar mensaje de error si existe
                if (isset($_SESSION['error'])) {
                    echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
                    unset($_SESSION['error']); // Borramos el error después de mostrarlo
                }
                ?>
                
                <div class="vc-product-card">
                    <div class="w-100">
                        <form action="login.php" method="post">
                            <div class="mb-3">
                                <label for="usuario" class="form-label">Usuario:</label>
                                <input type="text" class="form-control" id="usuario" name="usuario" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña:</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">Entrar</button>
                        </form>
                    </div>
                </div>
                
                <div class="vc-footer mt-4">
                    <small>Usa: admin/admin o usuario/usuario</small>
                </div>
            </div>
        </div>
    </div>
</body>
</html>