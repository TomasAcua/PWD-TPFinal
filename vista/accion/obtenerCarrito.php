<?php
include_once '../config/config.php';

session_start();
$idCompra = $_SESSION['idcompra'] ?? null;

if ($idCompra) {
    $compraController = new CompraController();
    $productos = $compraController->obtenerProductosCarrito($idCompra);

    echo json_encode($productos);
} else {
    echo json_encode([]);
}
?>
