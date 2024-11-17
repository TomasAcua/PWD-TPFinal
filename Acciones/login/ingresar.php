<?php
include_once "../../configuracion.php";

// Configurar el logging
ini_set('display_errors', 1);
ini_set('log_errors', 1);
// Asegúrate de que esta ruta sea escribible
ini_set('error_log', __DIR__ . '/login-debug.log');

// Log inicial para verificar que el script se está ejecutando
error_log("=== INICIO DE SOLICITUD DE LOGIN ===");

// Limpiar cualquier salida previa
ob_start();

header('Content-Type: application/json');

$respuesta = [
    'success' => false,
    'message' => '',
    'debug' => []
];

try {
    error_log("Obteniendo datos enviados...");
    $datos = data_submitted();
    error_log("Datos recibidos: " . print_r($datos, true));
    
    if (isset($datos['usnombre']) && isset($datos['uspass'])) {
        error_log("Datos completos recibidos para usuario: " . $datos['usnombre']);
        
        $sesion = new Session();
        error_log("Sesión creada");
        
        // Verificar estado de sesión
        $sesionActiva = $sesion->activa();
        error_log("Estado de sesión activa: " . ($sesionActiva ? 'SI' : 'NO'));
        
        if (!$sesionActiva) {
            error_log("Intentando validar usuario...");
            
            // Primero validamos
            $validacion = $sesion->validar($datos['usnombre'], $datos['uspass']);
            error_log("Resultado de validación: " . ($validacion ? 'EXITOSA' : 'FALLIDA'));
            
            // Luego intentamos iniciar sesión
            if ($validacion && $sesion->iniciar($datos['usnombre'], $datos['uspass'])) {
                error_log("Inicio de sesión exitoso");
                $respuesta['success'] = true;
                $respuesta['message'] = 'Inicio de sesión exitoso';
            } else {
                error_log("Falló el inicio de sesión");
                $respuesta['message'] = 'Usuario o contraseña incorrectos';
                $respuesta['debug']['validation_failed'] = true;
            }
        } else {
            error_log("Ya existe una sesión activa");
            $respuesta['message'] = 'Ya existe una sesión activa';
        }
    } else {
        error_log("Datos incompletos recibidos");
        $respuesta['message'] = 'Datos incompletos';
    }
    
    $respuesta['debug']['datos_recibidos'] = $datos;
    
} catch (Exception $e) {
    error_log("Error en ingresar.php: " . $e->getMessage());
    $respuesta['message'] = 'Error del servidor: ' . $e->getMessage();
    $respuesta['debug']['error'] = $e->getMessage();
}

// Log final
error_log("Respuesta a enviar: " . print_r($respuesta, true));
error_log("=== FIN DE SOLICITUD DE LOGIN ===\n");

// Limpiar buffer y enviar respuesta
ob_clean();
echo json_encode($respuesta);
exit;
?>