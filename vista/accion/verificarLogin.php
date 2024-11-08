<?php
require_once '../control/UsuarioController.php';

$nombre = $_POST['nombre'];
$password = $_POST['password'];

$usuarioController = new UsuarioController();
if ($usuarioController->iniciarSesion($nombre, $password)) {
    header("Location: ../vista/bienvenido.php");
} else {
    header("Location: ../vista/login.php?error=1");
}
exit();
