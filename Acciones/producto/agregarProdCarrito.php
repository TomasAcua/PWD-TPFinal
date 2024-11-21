<?php 
require_once "../../configuracion.php";

// Asegurarnos de que no haya salida antes del JSON
ob_start();

try {
    header('Content-Type: application/json');
    
    $data = data_submitted();
    
    if (!isset($data['idproducto']) || !isset($data['cicantidad'])) {
        throw new Exception("Datos incompletos");
    }
    
    $abmCompra = new abmCompra();
    $resultado = $abmCompra->agregarProdCarrito($data);
    
    // Limpiar cualquier salida anterior
    ob_clean();
    
    if ($resultado) {
        echo json_encode([
            'success' => true,
            'message' => 'Producto agregado al carrito correctamente'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No se pudo agregar el producto al carrito'
        ]);
    }

} catch (Exception $e) {
    // Limpiar cualquier salida anterior
    ob_clean();
    
    error_log("Error en agregarProdCarrito: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

ob_end_flush();
exit();
?>