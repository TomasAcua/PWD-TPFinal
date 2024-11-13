<?php
include_once '../../config/config.php';

$productoController = new ProductoController();
echo json_encode($productoController->listarProductos());
