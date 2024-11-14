<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ajustar Inventario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h2>Ajustar Inventario</h2>
    <form id="formAjustarInventario">
        <label>Producto ID:</label>
        <input type="text" name="idproducto" id="idproducto" required>
        <label>Nueva Cantidad:</label>
        <input type="number" name="cantidad" id="cantidad" required>
        <button type="button" onclick="ajustarInventario()">Actualizar Stock</button>
    </form>
    <div id="resultado"></div>

    <script>
        function ajustarInventario() {
            const data = {
                idproducto: $('#idproducto').val(),
                cantidad: $('#cantidad').val()
            };

            $.ajax({
                url: '../accion/ajustarInventario.php',
                type: 'POST',
                data: data,
                success: function(response) {
                    $('#resultado').html('<p>Inventario ajustado correctamente.</p>');
                },
                error: function() {
                    $('#resultado').html('<p>Error al ajustar el inventario.</p>');
                }
            });
        }
    </script>
</body>
</html>
