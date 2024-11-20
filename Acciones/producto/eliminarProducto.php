<?php
include_once "../../configuracion.php";
$data = data_submitted();
$objControl = new abmProducto();
$respuesta = false;

if (isset($data['idproducto'])) {
    error_log("Recibida solicitud para deshabilitar producto ID: " . $data['idproducto']);
    try {
        $respuesta = $objControl->baja($data);
        error_log("Resultado de la deshabilitación: " . ($respuesta ? "exitoso" : "fallido"));
    } catch (Exception $e) {
        error_log("Error al deshabilitar producto: " . $e->getMessage());
    }
}

header('Content-Type: application/json');
echo json_encode(['success' => $respuesta]);
?>