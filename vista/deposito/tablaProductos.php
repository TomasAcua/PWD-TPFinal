<?php
$Titulo = "Tabla Productos";
include_once '../Estructura/cabecera.php';

if (!$sesion->verificarPermiso('deposito/tablaproductos.php')) {
    $mensaje = "No tiene permiso para acceder a este sitio.";
    echo "<script> window.location.href='/TPFinal/Vista/index.php?mensaje=" . urlencode($mensaje) . "'</script>";
    exit;
}
?>

<!-- INCLUIMOS MODALES -->
<?php include '../Estructura/Modales/Productos/modal_add_producto.php'; ?>
<?php include '../Estructura/Modales/Productos/modal_editar_producto.php'; ?>
<?php include '../Estructura/Modales/Productos/modal_editar_imagen.php'; ?>

<div class="container my-2">
    <div class="d-flex justify-content-end mb-3">
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modal-add-producto">
            <i class="fas fa-plus me-2"></i>Agregar Producto
        </button>
    </div>
    <div class="table-responsive">
        <table class="table table-hover caption-top align-middle text-center" id="tablaProductos">
            <caption>Productos</caption>
            <thead class="table-dark">
                <tr>
                    <th width="70">ID</th>
                    <th>Nombre</th>
                    <th>Detalle</th>
                    <th>Stock</th>
                    <th>Precio</th>
                    <th>Imagen</th>
                    <th>Deshabilitado</th>
                    <th width="200">Acciones</th>
                </tr>
            </thead>
            <tbody class="table-group-divider">
            </tbody>
        </table>
    </div>
</div>

<!-- Scripts específicos de la página -->
<script>
    // Verificar que jQuery esté cargado
    if (typeof jQuery === 'undefined') {
        console.error('jQuery no está cargado!');
    }
</script>

<!-- Carga de scripts con rutas absolutas -->
<script src="/TPFinal/Utiles/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="/TPFinal/Utiles/bootstrap/js/bootstrapValidator.min.js"></script>
<script src="/TPFinal/Utiles/bootstrap/js/mensajesBVes_ES.js"></script>
<script src="/TPFinal/Utiles/bootboxjs-5.5.2/bootbox.min.js"></script>
<script src="/TPFinal/Utiles/Iconos/FontAwesomeKit.js"></script>
<script src="/TPFinal/Utiles/js/validaciones.js"></script>
<script src="/TPFinal/Utiles/js/funcionesABMProducto.js"></script>

<?php include_once '../Estructura/pie.php'; ?>