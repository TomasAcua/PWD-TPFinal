<?php
include_once '../config/config.php';
$usuarioController = new UsuarioController();
session_start();

// Verificar acceso para 'admin' y 'deposito'
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
    
    <!-- Tabla de productos -->
    <table id="dg" title="Listado de Productos" class="easyui-datagrid" style="width:700px;height:400px"
           url="../accion/obtenerProductos.php"  <!-- Archivo de datos -->
           toolbar="#toolbar" pagination="true" rownumbers="true" fitColumns="true" singleSelect="true">
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
    
    <div id="toolbar">
        <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="icon-add" plain="true" onclick="nuevoProducto()">Nuevo Producto</a>
    </div>

    <script>
        function nuevoProducto() {
            $('#dlg').dialog('open').dialog('setTitle', 'Nuevo Producto');
            $('#fm').form('clear');
        }

        function formatoAcciones(value, row, index) {
            return `<a href="javascript:void(0)" onclick="editarProducto(${row.idproducto})">Editar</a> | 
                    <a href="javascript:void(0)" onclick="eliminarProducto(${row.idproducto})">Eliminar</a>`;
        }

        function editarProducto(id) {
            // Lógica para abrir un diálogo de edición
        }

        function eliminarProducto(id) {
            $.post('../accion/eliminarProducto.php', { idproducto: id }, function(result) {
                $('#dg').datagrid('reload'); // Recargar la tabla
            });
        }
    </script>
</body>
</html>
