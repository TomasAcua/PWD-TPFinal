<?php
include_once "../../configuracion.php";
$data = data_submitted();
$objControl = new abmProducto();

$arreglo_salida = $objControl->listarProductos($data);

echo json_encode($arreglo_salida);
?>

