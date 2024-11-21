<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

include_once "../../configuracion.php";

header('Content-Type: application/json');

try {
    $data = data_submitted();
    error_log("Datos recibidos: " . print_r($data, true));
    error_log("Archivos recibidos: " . print_r($_FILES, true));
    
    $objControl = new abmProducto();
    $controlImg = new controlImagenes();
    $respuesta = false;
    $mensaje = "";

    // Validar datos requeridos
    if (!empty($data['pronombre']) && !empty($data['prodetalle']) && 
        isset($data['procantstock']) && isset($data['precio'])) {
        
        error_log("Validación de datos básicos correcta");
        
        // Validar y procesar imagen
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
            error_log("Procesando imagen...");
            $resultadoImagen = $controlImg->cargarImagen('producto', $_FILES['imagen'], 'productos/');
            
            if ($resultadoImagen['respuesta']) {
                $data['imagen'] = $resultadoImagen['nombre'];
                error_log("Imagen cargada exitosamente: " . $data['imagen']);
                
                // Preparar datos para el alta
                $datosAlta = [
                    'pronombre' => $data['pronombre'],
                    'prodetalle' => $data['prodetalle'],
                    'procantstock' => $data['procantstock'],
                    'precio' => $data['precio'],
                    'imagen' => $data['imagen'],
                    'prodeshabilitado' => null
                ];
                
                error_log("Intentando crear producto con datos: " . print_r($datosAlta, true));
                
                $respuesta = $objControl->alta($datosAlta);
                $mensaje = $respuesta ? "Producto creado correctamente" : "No se pudo crear el producto";
                
            } else {
                $mensaje = "Error al procesar la imagen";
                error_log("Error al procesar la imagen");
            }
        } else {
            $mensaje = "No se proporcionó una imagen válida";
            error_log("Error con la imagen: " . print_r($_FILES['imagen']['error'], true));
        }
    } else {
        $mensaje = "Faltan datos requeridos";
        error_log("Faltan datos requeridos en el formulario");
    }

    $response = [
        'success' => $respuesta,
        'message' => $mensaje
    ];
    
    error_log("Respuesta final: " . print_r($response, true));
    echo json_encode($response);

} catch (Exception $e) {
    error_log("Error en altaProducto.php: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    echo json_encode([
        'success' => false,
        'message' => "Error interno del servidor: " . $e->getMessage()
    ]);
}
?>