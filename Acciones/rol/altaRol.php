<?php 
include_once "../../configuracion.php";
$data = data_submitted();
$objAbmRol = new abmRol();
$arreglo = ['rodescripcion' => $data['rodescripcion']];

header('Content-Type: application/json');
$resultado = $objAbmRol->altaSinId($arreglo);
echo json_encode($resultado);
?>