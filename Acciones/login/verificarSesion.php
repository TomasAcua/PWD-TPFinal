<?php
include_once "../../configuracion.php";
header('Content-Type: application/json');

$sesion = new Session();
echo json_encode(['sesionActiva' => $sesion->activa()]);