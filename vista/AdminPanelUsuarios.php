<?php
require_once '../control/UsuarioController.php';
$usuarioController = new UsuarioController();
session_start();

// Verificar acceso solo para admin
if (!$usuarioController->tieneAcceso(['admin'])) {
    header("Location: acceso_denegado.php");
    exit();
}

$usuarios = $usuarioController->obtenerUsuarios(); // Función para obtener todos los usuarios

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios</title>
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
                    <form action="../accion/asignarRol.php" method="POST">
                        <input type="hidden" name="idusuario" value="<?php echo $usuario['idusuario']; ?>">
                        <select name="idrol">
                            <option value="1">Usuario</option>
                            <option value="2">Admin</option>
                            <option value="3">Deposito</option>
                        </select>
                        <button type="submit">Asignar Rol</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
