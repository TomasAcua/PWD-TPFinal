<?php
include_once '../../config/config.php';
$usuarioController = new UsuarioController();
session_start();

if (!$usuarioController->tieneAcceso(['deposito'])) {
    header("Location: ../vista/acceso_denegado.php");
    exit();
}

$idProducto = $_POST['idproducto'];
$cantidad = $_POST['cantidad'];

$productoController = new ProductoController();
$productoController->actualizarStock($idProducto, $cantidad);

header("Location: ../vista/ajustarInventario.php?mensaje=Stock actualizado");
