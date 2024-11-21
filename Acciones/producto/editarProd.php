<?php
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
        error_log("Recibida solicitud para editar producto ID: " . $data['idproducto']);
        
        // Buscar el producto existente
        $productoExistente = $objControl->buscar(['idproducto' => $data['idproducto']]);
        
        if (!empty($productoExistente)) {
            // Procesar imagen si se subió una nueva
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
                // Eliminar imagen anterior si existe
                if ($productoExistente[0]->getImagen()) {
                    $controlImg->eliminarImagen($productoExistente[0]->getImagen(), 'productos/');
                }
                
                // Cargar nueva imagen
                $resultadoImagen = $controlImg->cargarImagen('producto', $_FILES['imagen'], 'productos/');
                if ($resultadoImagen['respuesta']) {
                    $data['imagen'] = $resultadoImagen['nombre'];
                } else {
                    throw new Exception("Error al subir la nueva imagen");
                }
            } else {
                // Mantener imagen existente
                $data['imagen'] = $productoExistente[0]->getImagen();
            }
            
            // Mantener el estado de deshabilitación
            $data['prodeshabilitado'] = $productoExistente[0]->getProDeshabilitado();
            
            error_log("Intentando modificar con datos: " . print_r($data, true));
            
            $respuesta = $objControl->modificacion($data);
            $mensaje = $respuesta ? "Producto modificado correctamente" : "No se pudo modificar el producto";
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
    error_log("Error en editarProd.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => "Error interno del servidor: " . $e->getMessage()
    ]);
}
?>