<?php
require_once '../control/CompraController.php';
require_once '../control/CompraEstadoController.php';

session_start();
$idCompra = $_SESSION['idcompra']; // ID de la compra actual
$compraEstadoController = new CompraEstadoController();

// Cambiar el estado de la compra a 'Iniciada'
$compraEstadoController->cambiarEstadoCompra($idCompra, 1);

// Limpiar el carrito
unset($_SESSION['idcompra']); // O cualquier otro proceso de limpieza

header("Location: ../vista/compraConfirmada.php");
exit();
