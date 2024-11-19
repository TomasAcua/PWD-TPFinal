<?php
include_once "../../configuracion.php";
$data = data_submitted();
$obj = new abmUsuario();
$respuesta = false;

error_log("Datos recibidos: " . print_r($data, true));

if (isset($data['accion']) && isset($data['idusuario'])) {
    if ($data['accion'] === 'habilitar') {
        $respuesta = $obj->habilitarUsuario($data);
    } else if ($data['accion'] === 'deshabilitar') {
        $respuesta = $obj->deshabilitarUsuario($data);
    }
}

error_log("Respuesta: " . ($respuesta ? "true" : "false"));
echo json_encode($respuesta);
