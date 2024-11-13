<?php
include_once '../config/config.php';

$usuarioController = new UsuarioController();
session_start();

// Verificar que solo el rol admin tenga acceso
if (!$usuarioController->tieneAcceso(['admin'])) {
    header("Location: acceso_denegado.php");
    exit();
}

$compraController = new CompraController();
$compras = $compraController->obtenerTodasLasCompras();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración de Compras</title>
</head>
<body>
    <h2>Panel de Administración de Compras</h2>
    <table border="1">
        <tr>
            <th>ID Compra</th>
            <th>Usuario</th>
            <th>Fecha</th>
            <th>Estado</th>
            <th>Cambiar Estado</th>
        </tr>
        <?php foreach ($compras as $compra): ?>
            <tr>
                <td><?php echo $compra['idcompra']; ?></td>
                <td><?php echo $compra['idusuario']; ?></td>
                <td><?php echo $compra['cofecha']; ?></td>
                <td><?php echo $compra['estado']; ?></td>
                <td>
                    <form action="../accion/cambiarEstadoCompra.php" method="POST">
                        <input type="hidden" name="idcompra" value="<?php echo $compra['idcompra']; ?>">
                        <select name="nuevoEstado">
                            <option value="1">Iniciada</option>
                            <option value="2">Aceptada</option>
                            <option value="3">Enviada</option>
                            <option value="4">Cancelada</option>
                        </select>
                        <button type="submit">Actualizar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
