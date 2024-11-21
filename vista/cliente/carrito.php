<?php
$Titulo = "Carrito";
include_once '../Estructura/cabecera.php';
if (!$sesion->verificarPermiso('Cliente/carrito.php')) {
    $mensaje = "No tiene permiso para acceder a este sitio.";
    echo "<script> window.location.href='/TPFinal/Vista/index.php?mensaje=" . urlencode($mensaje) . "'</script>";
} else {
?>
    <div class="container py-5 text-center">
        <div class="row align-items-start">
            <div class="table-responsive col-9" id="estructuraCarrito">
                <table class="table table-hover caption-top align-middle text-center" id="tablaCarrito">
                </table>
            </div>
            <div class="col-3 align-self-start" id="totalPagar">
            </div>
        </div>

        <!-- Mensaje cuando el carrito está vacío -->
        <div id="carritoVacio" class="d-none">
            <div class="card mb-3 mx-auto" style="max-width: 540px;">
                <div class="row g-0">
                    <div class="col-md-4">
                        <img src="../img/carrito-vacio.png" class="img-fluid rounded-start" alt="Carrito vacío">
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">
                            <h4 class="card-title">Tu carrito está vacío</h4>
                            <p class="card-text">¡Visita nuestra tienda para encontrar productos increíbles!</p>
                            <p class="card-text">
                                <a href="./tienda.php">
                                    <button class="btn btn-primary">
                                        <i class="fas fa-store me-2"></i>Ir a la tienda
                                    </button>
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php } ?>
<script src="../Utiles/js/funcionesCarrito.js"></script>
<?php include_once '../Estructura/pie.php'; ?>