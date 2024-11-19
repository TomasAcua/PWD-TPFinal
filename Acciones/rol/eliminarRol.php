<?php
include_once "../../configuracion.php";
$data = data_submitted();
$objAbmRol = new abmRol();

header('Content-Type: application/json');
$resultado = $objAbmRol->baja($data);
echo json_encode($resultado);
?>