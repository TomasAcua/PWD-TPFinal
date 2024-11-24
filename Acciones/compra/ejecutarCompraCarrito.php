<?php

include_once "../../configuracion.php";
header('Content-Type: application/json');

try {
    $data = data_submitted();
    
    if (!isset($data['idcompra'])) {
        throw new Exception("ID de compra no proporcionado");
    }
    
    $objCompra = new abmCompra();
    $resultado = $objCompra->aceptarCarrito($data);
    
    echo json_encode([
        'respuesta' => $resultado,
        'mensaje' => $resultado ? 'Compra aceptada correctamente' : 'No se pudo aceptar la compra'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'respuesta' => false,
        'mensaje' => $e->getMessage()
    ]);
}

?>


