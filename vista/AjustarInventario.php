<?php
include_once '../config/config.php';
$usuarioController = new UsuarioController();
session_start();

// Verificar que solo los usuarios de depósito tengan acceso
if (!$usuarioController->tieneAcceso(['deposito'])) {
    header("Location: acceso_denegado.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ajustar Inventario</title>
</head>
<body>
    <h2>Ajustar Inventario</h2>
    <form method="POST" action="../accion/ajustarInventario.php">
        <!-- Formulario para ajustar inventario -->
        <label>Producto ID:</label>
        <input type="text" name="idproducto">
        <label>Nueva Cantidad:</label>
        <input type="number" name="cantidad">
        <button type="submit">Actualizar Stock</button>
    </form>
</body>
</html>
