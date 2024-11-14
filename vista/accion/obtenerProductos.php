<?php
include_once '../../config/config.php';

$productoController = new ProductoController();
$productos = $productoController->obtenerProductos();
echo json_encode($productos);
?>
