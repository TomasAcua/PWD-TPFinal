<?php
include_once '../config/config.php';
$usuarioController = new UsuarioController();
session_start();

if (!$usuarioController->tieneAcceso(['admin', 'deposito'])) {
    header("Location: acceso_denegado.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Productos</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <h2>Productos Disponibles</h2>
    <table id="dg" title="Listado de Productos" class="easyui-datagrid" style="width:700px;height:400px"
           url="../accion/obtenerProductos.php" toolbar="#toolbar" pagination="true" rownumbers="true" fitColumns="true" singleSelect="true">
           <thead>
            <tr>
                <th field="idproducto" width="50">ID</th>
                <th field="pronombre" width="100">Nombre</th>
                <th field="prodetalle" width="150">Detalle</th>
                <th field="procantstock" width="50">Stock</th>
                <th field="accion" width="100" formatter="formatoAcciones">Acciones</th>
            </tr>
        </thead>
    </table>

    <script>
        function eliminarProducto(id) {
            fetch('../accion/eliminarProducto.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ idproducto: id })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) $('#dg').datagrid('reload');
                else alert('Error al eliminar el producto.');
            });
        }
    </script>
</body>
</html>
