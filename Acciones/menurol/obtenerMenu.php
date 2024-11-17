<?php
include_once "../../configuracion.php";
header('Content-Type: application/json');

$response = [
    'permisos' => [],
    'roles' => [],
    'usuario' => ['nombre' => '', 'rol' => ''],
    'error' => false,
    'message' => ''
];

$sesion = new Session();
if (!$sesion->activa()) {
    $response['error'] = true;
    $response['message'] = 'No hay sesión activa';
    echo json_encode($response);
    exit;
}

// Si hay sesión activa, obtener el menú
$abmMenu = new abmMenu();
$menuData = $abmMenu->armarMenu();

echo json_encode($menuData);
exit;