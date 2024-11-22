<?php
$Titulo = "Tienda";
include_once '../Estructura/cabecera.php';
if (!$sesion->verificarPermiso('Cliente/tienda.php')) {
    $mensaje = "No tiene permiso para acceder a este sitio.";
    echo "<script> window.location.href='/TPFinal/Vista/index.php?mensaje=" . urlencode($mensaje) . "'</script>";
} else {
    $objProducto = new abmProducto();
    $listaProductos = $objProducto->buscar(null);
?>

<div class="container py-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="text-center"><i class="fas fa-store me-2"></i>Nuestra Tienda</h2>
        </div>
        <div class="col-auto">
            <a href="carrito.php" class="btn btn-primary">
                <i class="fas fa-shopping-cart me-2"></i>Ver Carrito
            </a>
        </div>
    </div>
    
    <!-- Contenedor de productos -->
    <div class="row">
        <?php foreach ($listaProductos as $producto) { ?>
            <div class="col-md-3 mb-4">
                <div class="card h-100">
                    <img src="../img/productos/<?php echo $producto->getImagen() ?>" 
                         class="card-img-top" 
                         alt="<?php echo $producto->getProNombre() ?>"
                         style="height: 200px; object-fit: cover;">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?php echo $producto->getProNombre() ?></h5>
                        <p class="card-text"><?php echo $producto->getProDetalle() ?></p>
                        <div class="mt-auto">
                            <p class="card-text">
                                <strong class="text-primary">$<?php echo $producto->getPrecio() ?></strong>
                            </p>
                            <p class="card-text">
                                <small class="text-muted">Stock disponible: <?php echo $producto->getProCantStock() ?></small>
                            </p>
                            <?php if ($producto->getProCantStock() > 0) { ?>
                                <button class="btn btn-primary w-100 agregarCarrito" 
                                        data-id="<?php echo $producto->getID() ?>" 
                                        data-stock="<?php echo $producto->getProCantStock() ?>">
                                    <i class="fas fa-cart-plus me-2"></i>Agregar al carrito
                                </button>
                            <?php } else { ?>
                                <button class="btn btn-secondary w-100" disabled>
                                    <i class="fas fa-times-circle me-2"></i>Sin stock
                                </button>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
    
    <!-- Toast para notificaciones -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="notificacionCarrito" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-success text-white">
                <i class="fas fa-check-circle me-2"></i>
                <strong class="me-auto">Éxito</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                Producto agregado al carrito
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.agregarCarrito').click(function() {
        const idproducto = $(this).data('id');
        const stock = $(this).data('stock');
        const boton = $(this);
        
        if (stock > 0) {
            boton.prop('disabled', true);
            
            $.ajax({
                type: "POST",
                url: '/TPFinal/Acciones/producto/agregarProdCarrito.php',
                data: { 
                    idproducto: idproducto,
                    cicantidad: 1
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('.toast-body').text(response.message);
                        const toast = new bootstrap.Toast(document.getElementById('notificacionCarrito'));
                        toast.show();
                    } else {
                        bootbox.alert({
                            message: response.message || "No se pudo agregar el producto al carrito",
                            className: 'rubberBand animated'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    console.error('Respuesta del servidor:', xhr.responseText);
                    
                    let mensaje = "Error al agregar al carrito";
                    try {
                        const respuesta = JSON.parse(xhr.responseText);
                        mensaje = respuesta.message || mensaje;
                    } catch(e) {
                        console.error('Error al parsear respuesta:', e);
                    }
                    
                    bootbox.alert({
                        message: mensaje,
                        className: 'rubberBand animated'
                    });
                },
                complete: function() {
                    boton.prop('disabled', false);
                }
            });
        }
    });
});
</script>

<?php 
}
include_once '../Estructura/pie.php'; 
?>