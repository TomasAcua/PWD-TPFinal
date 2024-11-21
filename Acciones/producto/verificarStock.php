<?php
include_once "../../configuracion.php";
header('Content-Type: application/json');

try {
    if (!isset($_POST['idcompra'])) {
        throw new Exception("ID de compra no proporcionado");
    }
    
    $idcompra = $_POST['idcompra'];
    $abmCompraItem = new abmCompraItem();
    $items = $abmCompraItem->buscar(['idcompra' => $idcompra]);
    
    $stockInsuficiente = [];
    foreach ($items as $item) {
        $producto = $item->getObjProducto();
        $cantidadSolicitada = $item->getCiCantidad();
        $stockDisponible = $producto->getProCantStock();
        
        if ($cantidadSolicitada > $stockDisponible) {
            $stockInsuficiente[] = [
                'producto' => $producto->getProNombre(),
                'solicitado' => $cantidadSolicitada,
                'disponible' => $stockDisponible
            ];
        }
    }
    
    echo json_encode([
        'exito' => empty($stockInsuficiente),
        'productosInsuficientes' => $stockInsuficiente
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'exito' => false,
        'error' => $e->getMessage()
    ]);
}
?>
