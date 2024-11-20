<?php
include_once "../../configuracion.php";
$data = data_submitted();
$objControl = new abmProducto();

// Si hay una nueva imagen
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
    $data['imagen'] = $_FILES['imagen'];
}

$resultado = $objControl->modificacion($data);
echo json_encode(['success' => $resultado]);
?>