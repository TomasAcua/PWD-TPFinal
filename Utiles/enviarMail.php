<?php
include_once __DIR__ . '/../configuracion.php';
include_once __DIR__ . '/funcionesMailer.php';

header('Content-Type: application/json');

try {
    $data = data_submitted();
    
    if (!isset($data['idcompra']) || !isset($data['idcompraestadotipo'])) {
        throw new Exception("Faltan datos necesarios");
    }
    $user = buscarUsuario($idcompra);
    $idcompra = $data['idcompra'];
    $idcompraestadotipo = $data['idcompraestadotipo'];
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