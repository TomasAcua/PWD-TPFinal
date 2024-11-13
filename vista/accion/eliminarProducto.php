<?php
include_once '../../config/config.php';

$idproducto = $_POST['idproducto'];
$productoController = new ProductoController();
$resultado = $productoController->eliminarProducto($idproducto);

echo json_encode(['success' => $resultado]);