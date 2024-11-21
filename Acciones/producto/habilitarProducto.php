<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

include_once "../../configuracion.php";

header('Content-Type: application/json');

try {
    $data = data_submitted();
    $objControl = new abmProducto();
    $respuesta = false;
    $mensaje = "";

    if (isset($data['idproducto'])) {
        error_log("Recibida solicitud para habilitar producto ID: " . $data['idproducto']);
        
        // Buscar el producto primero
        $producto = $objControl->buscar(['idproducto' => $data['idproducto']]);
        
        if (!empty($producto)) {
            // Preparar datos para la modificación
            $datosModificacion = [
                'idproducto' => $data['idproducto'],
                'pronombre' => $producto[0]->getProNombre(),
                'prodetalle' => $producto[0]->getProDetalle(),
                'procantstock' => $producto[0]->getProCantStock(),
                'precio' => $producto[0]->getPrecio(),
                'prodeshabilitado' => null,
                'imagen' => $producto[0]->getImagen()
            ];
            
            error_log("Intentando habilitar con datos: " . print_r($datosModificacion, true));
            
            $respuesta = $objControl->modificacion($datosModificacion);
            $mensaje = $respuesta ? "Producto habilitado correctamente" : "No se pudo habilitar el producto";
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
    error_log("Error en habilitarProducto.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => "Error interno del servidor: " . $e->getMessage()
    ]);
}
?>