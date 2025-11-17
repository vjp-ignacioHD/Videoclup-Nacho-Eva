<?php
session_start();

// Solo admin puede acceder
if (!isset($_SESSION['usuario']) || $_SESSION['usuario'] !== 'admin') {
    header('Location: index.php');
    exit();
}

// Recuperar datos del formulario
$nombre = trim($_POST['nombre'] ?? '');
$user = trim($_POST['user'] ?? '');
$email = trim($_POST['email'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');

// Validaciones básicas
$error = null;

if (empty($nombre) || empty($user) || empty($email) || empty($telefono)) {
    $error = "Todos los campos son obligatorios.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = "El email no es válido.";
} else {
    // Verificar que el 'user' no exista ya
    $user_existe = false;
    if (isset($_SESSION['clientes'])) {
        foreach ($_SESSION['clientes'] as $cliente) {
            if ($cliente['user'] === $user) {
                $user_existe = true;
                break;
            }
        }
    }
    if ($user_existe) {
        $error = "El nombre de usuario ya está en uso.";
    }
}

if ($error) {
    $_SESSION['error_create'] = $error;
    header('Location: formCreateCliente.php');
    exit();
}

// Generar nuevo ID
$next_id = 1;
if (!empty($_SESSION['clientes'])) {
    $ids = array_column($_SESSION['clientes'], 'id');
    $next_id = max($ids) + 1;
}

// Añadir nuevo cliente
$nuevo_cliente = [
    'id' => $next_id,
    'nombre' => $nombre,
    'user' => $user,
    'email' => $email,
    'telefono' => $telefono
];

$_SESSION['clientes'][] = $nuevo_cliente;

// Redirigir al panel de admin
header('Location: mainAdmin.php');
exit();
?>