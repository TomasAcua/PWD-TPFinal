<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración de Compras</title>
    <link rel="stylesheet" href="css/estilos.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                    <select onchange="cambiarEstadoCompra(<?php echo $compra['idcompra']; ?>, this.value)">
                        <option value="1">Iniciada</option>
                        <option value="2">Aceptada</option>
                        <option value="3">Enviada</option>
                        <option value="4">Cancelada</option>
                    </select>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <script>
        function cambiarEstadoCompra(idCompra, nuevoEstado) {
            $.post('../accion/cambiarEstadoCompra.php', { idcompra: idCompra, nuevoEstado: nuevoEstado }, function(response) {
                alert('Estado de compra actualizado correctamente');
            }).fail(function() {
                alert('Error al actualizar el estado de compra');
            });
        }
    </script>
</body>
</html>
