<?php
include_once "../../configuracion.php";
header('Content-Type: application/json');

try {
    $data = data_submitted();
    
    if (!isset($data['idcompra']) || !isset($data['idcompraestadotipo'])) {
        throw new Exception("Faltan datos necesarios");
    }

    $objCE = new abmCompraEstado();
    $resultado = $objCE->modificarEstado($data);
    
    if ($resultado) {
        echo json_encode([
            'exito' => true,
            'mensaje' => 'Estado modificado correctamente'
        ]);
    } else {
        throw new Exception("No se pudo modificar el estado");
    }
    
} catch (Exception $e) {
    echo json_encode([
        'exito' => false,
        'mensaje' => $e->getMessage()
    ]);
}
?>