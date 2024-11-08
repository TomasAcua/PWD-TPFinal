<?php
require_once '../control/ProductoController.php';

$productoController = new ProductoController();
echo json_encode($productoController->listarProductos());
