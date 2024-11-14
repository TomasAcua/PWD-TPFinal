<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carrito de Compras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h2>Carrito de Compras</h2>
    <table border="1" id="tablaCarrito">
        <tr>
            <th>Producto</th>
            <th>Detalle</th>
            <th>Cantidad</th>
        </tr>
        <!-- Los productos se cargarán dinámicamente aquí -->
    </table>
    <button onclick="finalizarCompra()">Finalizar Compra</button>
    <div id="mensajeCompra"></div>

    <script>
        $(document).ready(function() {
            cargarCarrito();
        });

        function cargarCarrito() {
            $.ajax({
                url: '../accion/obtenerCarrito.php',
                type: 'GET',
                success: function(response) {
                    const productos = JSON.parse(response);
                    let html = '';

                    productos.forEach(producto => {
                        html += `<tr>
                                    <td>${producto.pronombre}</td>
                                    <td>${producto.prodetalle}</td>
                                    <td>${producto.cicantidad}</td>
                                </tr>`;
                    });

                    $('#tablaCarrito').append(html);
                },
                error: function() {
                    $('#mensajeCompra').html('<p>Error al cargar el carrito.</p>');
                }
            });
        }

        function finalizarCompra() {
            $.ajax({
                url: '../accion/finalizarCompra.php',
                type: 'POST',
                success: function() {
                    $('#mensajeCompra').html('<p>Compra finalizada con éxito.</p>');
                },
                error: function() {
                    $('#mensajeCompra').html('<p>Error al finalizar la compra.</p>');
                }
            });
        }
    </script>
</body>
</html>
