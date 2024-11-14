<?php
include_once '../config/config.php';

$productosController = new ProductoController();
$productos = $productosController->obtenerProductos(); // Cambiado el nombre aquí
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tienda</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
</head>
<body>
    <?php include '../config/navbar.php'; ?>
    <div class="container mt-5">
        <h2 class="text-center">Tienda de Productos</h2>
        
        <!-- Productos disponibles -->
        <div class="row">
            <?php
            foreach ($productos as $producto) {
                echo '
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">' . $producto['pronombre'] . '</h5>
                            <p class="card-text">' . $producto['prodetalle'] . '</p>
                            <p class="card-text"><strong>Precio: </strong>' . $producto['precio'] . ' USD</p>
                            <form action="accion/agregarCarrito.php" method="POST">
                                <input type="hidden" name="producto_id" value="' . $producto['idproducto'] . '">
                                <button type="submit" class="btn btn-primary">Agregar al Carrito</button>
                            </form>
                        </div>
                    </div>
                </div>';
            }
            ?>
        </div>
    </div>
</body>
</html>
