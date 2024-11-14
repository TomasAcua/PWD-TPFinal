<?php
include_once '../../config/config.php';

$idUsuario = $_POST['idusuario'];
$idRol = $_POST['idrol'];

$usuarioController = new UsuarioController();
$resultado = $usuarioController->asignarRol($idUsuario, $idRol);

echo json_encode(['success' => $resultado, 'message' => $resultado ? 'Rol asignado correctamente' : 'Error al asignar rol']);
