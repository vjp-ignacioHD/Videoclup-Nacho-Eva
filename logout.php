<?php
session_start();

// Borramos TODOS los datos de la sesión
session_destroy();

// Volvemos a la página de login
header('Location: index.php');
exit();
?>