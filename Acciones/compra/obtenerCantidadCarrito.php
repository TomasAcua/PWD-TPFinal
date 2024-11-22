<?php
include_once '../../configuracion.php';

header('Content-Type: application/json');

try {
    $session = new Session();
    $idUsuario = $session->getIDUsuarioLogueado();
    
    if (!$idUsuario) {
        throw new Exception("Usuario no logueado");
    }

    $abmCompra = new abmCompra();
    $carrito = $abmCompra->obtenerCarrito($idUsuario);
    
    $cantidad = 0;
    if ($carrito) {
        $productos = $abmCompra->listadoProdCarrito($carrito);
        foreach ($productos as $producto) {
            $cantidad += $producto['cicantidad'];
        }
    }
    
    echo json_encode(['cantidad' => $cantidad]);

} catch (Exception $e) {
    echo json_encode(['cantidad' => 0]);
}
?>