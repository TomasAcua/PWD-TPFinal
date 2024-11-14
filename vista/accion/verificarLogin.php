<?php
// verificarLogin.php
include_once '../../config/config.php';

$datos = darDatosSubmitted();
$nombre = $datos['nombre'];
$password = $datos['password'];

$usuarioController = new UsuarioController();
if ($usuarioController->iniciarSesion($nombre, $password)) {
    // Asegúrate de que se configuren las variables de sesión correctamente
    $_SESSION['usuario'] = $nombre;  // Ejemplo de configuración de sesión
    $_SESSION['idusuario'] = $usuarioController->obtenerIdUsuario($nombre); // Asumiendo que obtienes el id en el controller
    $_SESSION['rol'] = $usuarioController->obtenerRol($nombre); // Guarda el rol si es necesario

    header("Location: ../tienda.php");  // Redirige a la tienda si el login es exitoso
} else {
    header("Location: ../vista/login.php?error=1"); // Login fallido
}
exit();

