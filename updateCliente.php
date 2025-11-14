<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit();
}

$usuario_actual = $_SESSION['usuario'];
$es_admin = ($usuario_actual === 'admin');

// Recuperar datos del formulario
$id = $_POST['id'] ?? null;
$nombre = trim($_POST['nombre'] ?? '');
$user = trim($_POST['user'] ?? '');
$email = trim($_POST['email'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');

// Validaciones
if ($id === null || empty($nombre) || empty($user) || empty($email) || empty($telefono)) {
    $_SESSION['error_update'] = "Todos los campos son obligatorios.";
    header("Location: formUpdateCliente.php?id=$id");
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error_update'] = "El email no es válido.";
    header("Location: formUpdateCliente.php?id=$id");
    exit();
}

// Buscar el cliente a actualizar
$cliente_index = null;
foreach ($_SESSION['clientes'] as $index => $cliente) {
    if ($cliente['id'] == $id) {
        $cliente_index = $index;
        break;
    }
}
if ($cliente_index === null) {
    $_SESSION['error_update'] = "Cliente no encontrado.";
    header('Location: mainAdmin.php');
    exit();
}

// Si no es admin, verificar que es su propio perfil
if (!$es_admin && $_SESSION['clientes'][$cliente_index]['user'] !== $usuario_actual) {
    header('Location: mainCliente.php');
    exit();
}

// Verificar que el nuevo 'user' no esté en uso (por otro cliente diferente)
$user_en_uso = false;
foreach ($_SESSION['clientes'] as $cliente) {
    if ($cliente['id'] != $id && $cliente['user'] === $user) {
        $user_en_uso = true;
        break;
    }
}
if ($user_en_uso) {
    $_SESSION['error_update'] = "El nombre de usuario ya está en uso.";
    header("Location: formUpdateCliente.php?id=$id");
    exit();
}

// Actualizar los datos
$_SESSION['clientes'][$cliente_index]['nombre'] = $nombre;
$_SESSION['clientes'][$cliente_index]['user'] = $user;
$_SESSION['clientes'][$cliente_index]['email'] = $email;
$_SESSION['clientes'][$cliente_index]['telefono'] = $telefono;

// Actualizar la sesión del usuario si es él mismo quien se edita
if (!$es_admin) {
    $_SESSION['usuario'] = $user; // por si cambió su nombre de usuario
}

// Redirigir
if ($es_admin) {
    header('Location: mainAdmin.php');
} else {
    header('Location: mainCliente.php');
}
exit();
?>