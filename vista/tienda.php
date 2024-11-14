<?php include_once '../config/config.php';

$productosController = new ProductoController();
$productos = $productoController->obtenerTodos();
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
        <div class="row">
            <?php foreach ($productos as $producto): ?>
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title"><?= $producto['pronombre'] ?></h5>
                            <p class="card-text"><?= $producto['prodetalle'] ?></p>
                            <button class="btn btn-primary add-to-cart" data-product-id="<?= $producto['idproducto'] ?>">Agregar al Carrito</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.dataset.productId;
                fetch('../accion/agregarCarrito.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ producto_id: productId })
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.success ? 'Producto agregado!' : 'Error al agregar.');
                });
            });
        });
    </script>
</body>
</html>
