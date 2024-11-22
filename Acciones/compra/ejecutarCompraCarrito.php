<?php

include_once "../../configuracion.php";
header('Content-Type: application/json');

try {
    $data = data_submitted();
    
    if (!isset($data['idcompra'])) {
        throw new Exception("ID de compra no proporcionado");
    }
    
    $objC = new abmCompra();
    $resultado = $objC->ejecutarCompraCarrito($data['idcompra']);
    
    echo json_encode([
        'respuesta' => $resultado,
        'mensaje' => 'Compra ejecutada correctamente'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'respuesta' => false,
        'mensaje' => $e->getMessage()
    ]);
}

?>
 


