<?php
include_once '../config/config.php';
$compraController = new CompraController();
session_start();

$idCompra = $_SESSION['idcompra']; // Supongamos que guardamos el ID de la compra en la sesión
$productos = $compraController->obtenerProductosCarrito($idCompra);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carrito de Compras</title>
</head>
<body>
    <h2>Carrito de Compras</h2>
    <table border="1">
        <tr>
            <th>Producto</th>
            <th>Detalle</th>
            <th>Cantidad</th>
        </tr>
        <?php foreach ($productos as $producto): ?>
            <tr>
                <td><?php echo $producto['pronombre']; ?></td>
                <td><?php echo $producto['prodetalle']; ?></td>
                <td><?php echo $producto['cicantidad']; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <form action="../accion/finalizarCompra.php" method="POST">
        <button type="submit">Finalizar Compra</button>
    </form>
</body>
</html>
