<?php
session_start();

// Incluimos la clase Cliente
require_once 'app/Cliente.php';

// Usamos el namespace
use Dwes\ProyectoVideoclub\Cliente;

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
    
    // Si es admin, cargamos datos especiales CON OBJETOS CLIENTE
    if ($usuario === 'admin') {
        
        // Creamos objetos Cliente con user y password
        $cliente1 = new Cliente("Juan Pérez", 1, "juanito", "clave123", 3);
        $cliente2 = new Cliente("María García", 2, "maria", "password456", 3);
        $cliente3 = new Cliente("Carlos López", 3, "carlos", "miclave789", 3);
        
        // Guardamos los objetos cliente en sesión
        $_SESSION['clientes'] = [$cliente1, $cliente2, $cliente3];
        
        // Los soportes los mantenemos como array asociativo por simplicidad
        $_SESSION['soportes'] = [
            [
                'id' => 1,
                'titulo' => 'El Padrino',
                'tipo' => 'DVD',
                'precio' => 15.99,
                'disponible' => true
            ],
            [
                'id' => 2,
                'titulo' => 'The Last of Us',
                'tipo' => 'Juego',
                'precio' => 49.99,
                'disponible' => false
            ],
            [
                'id' => 3,
                'titulo' => 'Pulp Fiction',
                'tipo' => 'DVD', 
                'precio' => 12.99,
                'disponible' => true
            ],
            [
                'id' => 4,
                'titulo' => 'FIFA 24',
                'tipo' => 'Juego',
                'precio' => 59.99,
                'disponible' => true
            ]
        ];
        
        // Redirigimos al admin a su página especial
        header('Location: mainAdmin.php');
    } else {
        // Usuario normal va a la página normal
        header('Location: main.php');
    }
    exit();
} else {
    // Login incorrecto - guardamos error
    $_SESSION['error'] = 'Usuario o contraseña incorrectos';
    
    // Volvemos al login
    header('Location: index.php');
    exit();
}
?>