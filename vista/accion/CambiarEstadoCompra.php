<?php
require_once '../control/CompraEstadoController.php';
session_start();

$idCompra = $_POST['idcompra'];
$nuevoEstado = $_POST['nuevoEstado'];

$compraEstadoController = new CompraEstadoController();
$compraEstadoController->cambiarEstadoCompra($idCompra, $nuevoEstado);

header("Location: ../vista/adminPanelCompras.php?mensaje=Estado actualizado");
exit();
