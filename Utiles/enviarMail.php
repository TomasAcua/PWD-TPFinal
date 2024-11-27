<?php
include_once __DIR__ . '/../configuracion.php';
include_once __DIR__ . '/funcionesMailer.php';

header('Content-Type: application/json');

try {
    $data = data_submitted();
    
    if (!isset($data['idcompra']) || !isset($data['idcompraestadotipo'])) {
        throw new Exception("Faltan datos necesarios");
    }

    // Primero obtenemos los valores de data
    $idcompra = $data['idcompra'];
    $idcompraestadotipo = $data['idcompraestadotipo'];
    
    // Luego buscamos el usuario
    $user = buscarUsuario($idcompra);
    
    if (!$user) {
        throw new Exception("No se encontró el usuario de la compra");
    }
    
    // Finalmente enviamos el mail
    $resultado = enviarMail($user, $idcompra, $idcompraestadotipo);
    
    echo json_encode([
        'exito' => $resultado,
        'mensaje' => $resultado ? 'Email enviado correctamente' : 'Error al enviar el email'
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'exito' => false,
        'mensaje' => $e->getMessage()
    ]);
}
?>