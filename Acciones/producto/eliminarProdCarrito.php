<?php
include_once "../../configuracion.php";
header('Content-Type: application/json');

try {
    $data = data_submitted();
    
    if (!isset($data['idcompraitem'])) {
        throw new Exception("ID del item no proporcionado");
    }
    
    $objCompraItem = new compraItem();
    $objCompraItem->setID($data['idcompraitem']);
    
    if ($objCompraItem->eliminar()) {
        echo json_encode([
            'success' => true,
            'message' => 'Producto eliminado del carrito correctamente'
        ]);
    } else {
        throw new Exception("No se pudo eliminar el producto del carrito");
    }
    
} catch (Exception $e) {
    error_log("Error en eliminarProdCarrito: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>