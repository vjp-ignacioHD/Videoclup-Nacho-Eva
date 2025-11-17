<?php
session_start();

// Solo accesible para admin
if (!isset($_SESSION['usuario']) || $_SESSION['usuario'] !== 'admin') {
    header('Location: index.php');
    exit();
}

$id = $_GET['id'] ?? null;
if ($id === null) {
    header('Location: mainAdmin.php');
    exit();
}

// Buscar índice del cliente
$index_a_eliminar = null;
foreach ($_SESSION['clientes'] as $index => $cliente) {
    if ($cliente['id'] == $id) {
        $index_a_eliminar = $index;
        break;
    }
}

// Si no encontramos el cliente, redirigimos
if ($index_a_eliminar === null) {
    header('Location: mainAdmin.php');
    exit();
}

// Eliminar el cliente del array
unset($_SESSION['clientes'][$index_a_eliminar]);
$_SESSION['clientes'] = array_values($_SESSION['clientes']); // Reindexar

// Redirigir a mainAdmin.php
header('Location: mainAdmin.php');
exit();
?>