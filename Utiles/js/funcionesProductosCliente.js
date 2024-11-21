$(document).ready(function() {
    cargarProductos();
});

function cargarProductos() {
    $.ajax({
        type: "POST",
        url: '../../Acciones/producto/listarProductos.php',
        success: function(response) {
            const productos = JSON.parse(response);
            mostrarProductos(productos);
        },
        error: function(error) {
            console.error('Error:', error);
            mostrarError("Error al cargar los productos");
        }
    });
}

function mostrarProductos(productos) {
    const contenedor = $('#filaProductos');
    contenedor.empty();

    productos.forEach(producto => {
        contenedor.append(`
            <div class="col-md-3 mb-4">
                <div class="card h-100">
                    <img src="../Vista/img/productos/${producto.imagen}" 
                         class="card-img-top" 
                         alt="${producto.pronombre}"
                         style="height: 200px; object-fit: cover;">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">${producto.pronombre}</h5>
                        <p class="card-text">${producto.prodetalle}</p>
                        <div class="mt-auto">
                            <p class="card-text">
                                <strong class="text-primary">$${producto.precio}</strong>
                            </p>
                            <p class="card-text">
                                <small class="text-muted">Stock disponible: ${producto.procantstock}</small>
                            </p>
                            ${generarBotonCompra(producto)}
                        </div>
                    </div>
                </div>
            </div>
        `);
    });
}

function generarBotonCompra(producto) {
    if (producto.procantstock > 0) {
        return `
            <button class="btn btn-primary w-100 agregarCarrito" 
                    data-id="${producto.idproducto}" 
                    data-stock="${producto.procantstock}">
                <i class="fas fa-cart-plus me-2"></i>Agregar al carrito
            </button>`;
    }
    return `
        <button class="btn btn-secondary w-100" disabled>
            <i class="fas fa-times-circle me-2"></i>Sin stock
        </button>`;
}

$(document).on('click', '.agregarCarrito', function() {
    const idproducto = $(this).data('id');
    const stock = $(this).data('stock');
    
    $.ajax({
        type: "POST",
        url: '../Acciones/producto/agregarProdCarrito.php',
        data: { 
            idproducto: idproducto,
            cicantidad: 1
        },
        success: function(response) {
            const resultado = JSON.parse(response);
            if (resultado) {
                mostrarNotificacion("Producto agregado al carrito");
            } else {
                mostrarError("No se pudo agregar el producto");
            }
        },
        error: function() {
            mostrarError("Error al agregar al carrito");
        }
    });
});

function mostrarNotificacion(mensaje) {
    $('.toast-body').text(mensaje);
    const toast = new bootstrap.Toast(document.getElementById('notificacionCarrito'));
    toast.show();
}

function mostrarError(mensaje) {
    bootbox.alert({
        message: mensaje,
        className: 'rubberBand animated'
    });
}