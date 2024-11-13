<?php
include_once '../../config/config.php';
session_start();

$idUsuario = $_POST['idusuario'];
$idRol = $_POST['idrol'];

$usuarioController = new UsuarioController();
$usuarioController->asignarRol($idUsuario, $idRol);

header("Location: ../vista/adminPanelUsuarios.php?mensaje=Rol asignado correctamente");
