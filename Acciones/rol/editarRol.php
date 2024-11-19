<?php
include_once "../../configuracion.php";
$data = data_submitted();
$arreglo = ['idrol' => $data['idrol'], 'rodescripcion' => $data['descripcion']];
$objAbmRol = new abmRol();

header('Content-Type: application/json');
$resultado = $objAbmRol->modificacion($arreglo);
echo json_encode($resultado);
?>