<?php
include_once '../../config/config.php';
include_once '../../control/CarritoController.php';

session_start();

// Validar que el usuario esté autenticado
if (!isset($_SESSION['idusuario'])) {
    header("Location: ../login.php");
    exit();
}

$datos = darDatosSubmitted();
$idUsuario = $_SESSION['idusuario'];
$idProducto = $datos['producto_id'];
$cantidad = isset($datos['cantidad']) ? intval($datos['cantidad']) : 1;

// Crear instancia de CarritoController
$carritoController = new CarritoController($db);

// Verificar si el usuario ya tiene una compra activa
$idCompra = $carritoController->crearCompra($idUsuario);

// Agregar el producto al carrito
$resultado = $carritoController->agregarProducto($idCompra, $idProducto, $cantidad);

if (isset($resultado['error'])) {
    $mensaje = $resultado['error'];
} else {
    $mensaje = "Producto agregado correctamente al carrito.";
}

// Redireccionar a la tienda con un mensaje de éxito o error
header("Location: ../tienda.php?mensaje=" . urlencode($mensaje));
exit();
