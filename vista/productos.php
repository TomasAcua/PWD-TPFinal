<?php
$Titulo = "Tienda";
include_once '../Estructura/cabecera.php';
if (!$sesion->verificarPermiso('Cliente/productos.php')) {
    $mensaje = "No tiene permiso para acceder a este sitio.";
    echo "<script> window.location.href='/TPFinal/Vista/index.php?mensaje=" . urlencode($mensaje) . "'</script>";
} else {
?>

<div class="container py-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="text-center"><i class="fas fa-store me-2"></i>Nuestra Tienda</h2>
        </div>
    </div>
    
    <!-- Contenedor de productos -->
    <div class="row" id="filaProductos"></div>
    
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

<script src="../Utiles/js/funcionesProductosCliente.js"></script>
<?php 
}
include_once '../Estructura/pie.php'; 
?>