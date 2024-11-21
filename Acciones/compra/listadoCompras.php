<?php
include_once "../../configuracion.php";

// Configuración de errores y headers
error_reporting(E_ALL);
ini_set('display_errors', 0); // Cambiamos a 0 para evitar que los errores se muestren en la respuesta
ini_set('log_errors', 1);

// Asegurarnos que no haya salida antes del JSON
ob_clean();
header('Content-Type: application/json');

try {
    error_log("=== INICIO listadoCompras.php ===");
    
    // Verificar conexión a la base de datos
    $base = new BaseDatos();
    if (!$base->Iniciar()) {
        throw new Exception("Error al conectar a la base de datos");
    }
    
    // Primero verificamos si hay compras
    $sql = "SELECT COUNT(*) as total FROM compra";
    error_log("Ejecutando SQL count: " . $sql);
    
    if (!$base->Ejecutar($sql)) {
        throw new Exception("Error al contar compras: " . $base->getError());
    }
    
    $row = $base->Registro();
    error_log("Total de compras encontradas: " . $row['total']);
    
    // Obtener todas las compras
    $sql = "SELECT c.*, ce.idcompraestado, cet.cetdescripcion 
            FROM compra c 
            LEFT JOIN compraestado ce ON c.idcompra = ce.idcompra 
            LEFT JOIN compraestadotipo cet ON ce.idcompraestadotipo = cet.idcompraestadotipo 
            WHERE (ce.cefechafin IS NULL OR ce.idcompra IS NULL)";
    
    error_log("Ejecutando SQL principal: " . $sql);
    
    if (!$base->Ejecutar($sql)) {
        throw new Exception("Error al buscar compras: " . $base->getError());
    }
    
    $resultado = [];
    while ($row = $base->Registro()) {
        error_log("Procesando compra ID: " . $row['idcompra']);
        
        // Obtener usuario
        $objUsuario = new usuario();
        $objUsuario->setID($row['idusuario']);
        $objUsuario->cargar();
        
        $resultado[] = [
            'idcompra' => $row['idcompra'],
            'cofecha' => $row['cofecha'],
            'usnombre' => $objUsuario->getUsNombre(),
            'estado' => isset($row['cetdescripcion']) ? $row['cetdescripcion'] : 'sin estado',
            'idcompraestado' => isset($row['idcompraestado']) ? $row['idcompraestado'] : null
        ];
    }
    
    error_log("Resultado final: " . print_r($resultado, true));
    
    if (empty($resultado)) {
        echo json_encode([]);
    } else {
        echo json_encode($resultado);
    }
    
} catch (Exception $e) {
    error_log("Error en listadoCompras.php: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'mensaje' => $e->getMessage()
    ]);
}

exit(); // Asegurarnos que nada más se ejecute después
?>
