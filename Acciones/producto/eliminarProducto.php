<?php
// Asegurarnos que no haya output antes de los headers
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

include_once "../../configuracion.php";

header('Content-Type: application/json');

try {
    $data = data_submitted();
    $objControl = new abmProducto();
    $controlImg = new controlImagenes();
    $respuesta = false;
    $mensaje = "";

    if (isset($data['idproducto'])) {
        error_log("Recibida solicitud para deshabilitar producto ID: " . $data['idproducto']);
        
        // Obtener información del producto antes de deshabilitarlo
        $producto = $objControl->buscar(['idproducto' => $data['idproducto']]);
        
        if (!empty($producto)) {
            // Intentar deshabilitar el producto
            $respuesta = $objControl->baja($data);
            
            if ($respuesta && !empty($producto[0]->getImagen())) {
                // Si se deshabilitó correctamente y tiene imagen, la eliminamos
                $controlImg->eliminarImagen($producto[0]->getImagen(), 'productos/');
            }
            
            $mensaje = $respuesta ? "Producto deshabilitado correctamente" : "No se pudo deshabilitar el producto";
        } else {
            $mensaje = "Producto no encontrado";
        }
    } else {
        $mensaje = "ID de producto no proporcionado";
    }

    echo json_encode([
        'success' => $respuesta,
        'message' => $mensaje
    ]);

} catch (Exception $e) {
    error_log("Error en eliminarProducto.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => "Error interno del servidor: " . $e->getMessage()
    ]);
}
?>