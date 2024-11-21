function comprar() {
    $.ajax({
        type: "POST",
        url: '../Acciones/compra/ejecutarCompraCarrito.php',
        success: function (response) {
            if (response) {
                bootbox.dialog({
                    title: '¡Compra Realizada!',
                    message: 'Su compra ha sido registrada exitosamente. Cuando el personal prepare y acepte su pedido, recibirá un correo electrónico de confirmación.',
                    buttons: {
                        ok: {
                            label: 'Entendido',
                            className: 'btn-success',
                            callback: function() {
                                window.location.href = './listaCompras.php';
                            }
                        }
                    }
                });
            } else {
                bootbox.alert({
                    message: "No se pudo realizar la compra",
                    className: 'rubberBand animated'
                });
            }
        }
    });
}

function cargarCarrito() {
    $.ajax({
        type: "POST",
        url: '../Acciones/compra/listadoProdCarrito.php',
        success: function(response) {
            const productos = JSON.parse(response);
            
            if (productos.length === 0) {
                // Mostrar mensaje de carrito vacío
                $('#estructuraCarrito').addClass('d-none');
                $('#totalPagar').addClass('d-none');
                $('#carritoVacio').removeClass('d-none');
            } else {
                // Mostrar productos en el carrito
                $('#estructuraCarrito').removeClass('d-none');
                $('#totalPagar').removeClass('d-none');
                $('#carritoVacio').addClass('d-none');
                actualizarTablaCarrito(productos);
            }
        },
        error: function(error) {
            console.error('Error:', error);
            bootbox.alert({
                message: "Error al cargar el carrito",
                className: 'rubberBand animated'
            });
        }
    });
}

// Asegúrate de llamar a cargarCarrito cuando se carga la página
$(document).ready(function() {
    cargarCarrito();
});

