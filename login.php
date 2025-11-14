<?php
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
    
    // Si es admin, cargamos datos especiales CON ARRAYS (no objetos)
    if ($usuario === 'admin') {
        
        // Creamos arrays con los datos de clientes (incluyendo user)
        $_SESSION['clientes'] = [
            [
                'id' => 1,
                'nombre' => 'Juan Pérez',
                'user' => 'juanito',      // NUEVO
                'email' => 'juan@email.com',
                'telefono' => '666111222'
            ],
            [
                'id' => 2, 
                'nombre' => 'María García',
                'user' => 'maria',        // NUEVO
                'email' => 'maria@email.com',
                'telefono' => '666333444'
            ],
            [
                'id' => 3,
                'nombre' => 'Carlos López',
                'user' => 'carlos',       // NUEVO
                'email' => 'carlos@email.com', 
                'telefono' => '666555666'
            ]
        ];
        
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