<?php
include_once '../../config/config.php';

$datos = darDatosSubmitted();
$nombre = $datos['nombre'];
$password = $datos['password'];

$usuarioController = new UsuarioController();

if ($usuarioController->iniciarSesion($nombre, $password)) {
    $_SESSION['usuario'] = $nombre;
    $_SESSION['idusuario'] = $usuarioController->obtenerIdUsuario($nombre);
    $_SESSION['rol'] = $usuarioController->obtenerRol($nombre);

    echo json_encode(['success' => true, 'redirect' => '../tienda.php']);
} else {
    echo json_encode(['success' => false, 'error' => 'Usuario o contraseña incorrectos']);
}
