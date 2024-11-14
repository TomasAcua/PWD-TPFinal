<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="css/estilos.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h2>Gestión de Usuarios</h2>
    <table border="1">
        <tr>
            <th>ID Usuario</th>
            <th>Nombre</th>
            <th>Rol</th>
            <th>Acciones</th>
        </tr>
        <?php foreach ($usuarios as $usuario): ?>
            <tr>
                <td><?php echo $usuario['idusuario']; ?></td>
                <td><?php echo $usuario['usnombre']; ?></td>
                <td><?php echo $usuario['rol']; ?></td>
                <td>
                    <select onchange="asignarRol(<?php echo $usuario['idusuario']; ?>, this.value)">
                        <option value="1">Usuario</option>
                        <option value="2">Admin</option>
                        <option value="3">Deposito</option>
                    </select>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <script>
        function asignarRol(idUsuario, idRol) {
            $.post('../accion/asignarRol.php', { idusuario: idUsuario, idrol: idRol }, function(response) {
                alert('Rol actualizado correctamente');
            }).fail(function() {
                alert('Error al actualizar el rol');
            });
        }
    </script>
</body>
</html>
