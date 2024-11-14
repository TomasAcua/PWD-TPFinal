<?php
include_once '../../config/config.php';

$idCompra = $_POST['idcompra'];
$nuevoEstado = $_POST['nuevoEstado'];

$compraEstadoController = new CompraEstadoController();
$resultado = $compraEstadoController->cambiarEstadoCompra($idCompra, $nuevoEstado);

echo json_encode(['success' => $resultado, 'message' => $resultado ? 'Estado actualizado' : 'Error al actualizar estado']);
