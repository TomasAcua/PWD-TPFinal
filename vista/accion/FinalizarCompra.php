<?php
include_once '../config/config.php';

session_start();
$idCompra = $_SESSION['idcompra'] ?? null;

if ($idCompra) {
    $compraController = new CompraController();
    $resultado = $compraController->finalizarCompra($idCompra);

    echo json_encode(['success' => $resultado ? true : false]);
} else {
    echo json_encode(['success' => false, 'message' => 'ID de compra no encontrado']);
}
?>
