<?php
// Iniciamos sesión
session_start();

// Lista de usuarios y contraseñas válidos
$usuarios_permitidos = [
    'admin' => 'admin',
    'usuario' => 'usuario'
];

// Recogemos los datos del formulario
$usuario = $_POST['usuario'];
$password = $_POST['password'];

// Comprobamos si el usuario existe y la contraseña es correcta
if (isset($usuarios_permitidos[$usuario]) && $usuarios_permitidos[$usuario] === $password) {
    // Login correcto - guardamos en sesión
    $_SESSION['usuario'] = $usuario;
    
    // Vamos a la página principal
    header('Location: main.php');
    exit();
} else {
    // Login incorrecto - guardamos error
    $_SESSION['error'] = 'Usuario o contraseña incorrectos';
    
    // Volvemos al login
    header('Location: index.php');
    exit();
}
?>