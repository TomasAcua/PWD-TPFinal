<?php
include_once "../../configuracion.php";
header('Content-Type: application/json');

try {
    $idcompra = $_POST['idcompra'];
    $abmCompraEstado = new abmCompraEstado();
    $estadoActual = $abmCompraEstado->obtenerEstadoActual($idcompra);
    
    if (in_array($estadoActual['descripcion'], ['enviada', 'cancelada'])) {
        throw new Exception("No se puede cancelar una compra " . $estadoActual['descripcion']);
    }
    
    // Cambiar estado a cancelada
    $abmCompraEstado->cambiarEstado($idcompra, 'cancelada');
    
    echo json_encode(['exito' => true]);
    
} catch (Exception $e) {
    echo json_encode([
        'exito' => false,
        'mensaje' => $e->getMessage()
    ]);
}
?>

