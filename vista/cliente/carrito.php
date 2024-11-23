<?php
include_once '../../configuracion.php';
$session = new Session();

// Verificar que el usuario esté logueado
if (!$session->activa()) {
    header('Location: ../login.php');
    exit;
}

// Verificar que sea un cliente
$rolActual = $session->getRolActivo();
if ($rolActual['rol'] !== 'cliente') {
    header('Location: ../login.php?error=No tiene permiso para acceder a esta página');
    exit;
}

$objUsuario = $session->getUsuario();
$abmCompra = new abmCompra();

try {
    // Buscar compra activa
    $compraActiva = $abmCompra->buscarCompraIniciada($objUsuario->getID());
    
    if ($compraActiva) {
        error_log("Compra activa encontrada: " . $compraActiva->getIdcompra());
        
        // Obtener items del carrito
        $compraItem = new compraItem();
        $where = "idcompra = " . $compraActiva->getIdcompra();
        $items = $compraItem->listar($where);
        
        error_log("Items encontrados: " . count($items));
        
        // Obtener detalles de los productos
        $productos = [];
        foreach ($items as $item) {
            // Corregir aquí: Obtener el ID del producto directamente
            $idProducto = $item->getObjProducto(); // Esto devuelve el ID directamente
            
            $producto = new producto();
            $producto->setID($idProducto);
            
            if ($producto->cargar()) {
                error_log("Producto cargado: " . $producto->getPronombre());
                $productos[] = [
                    'item' => $item,
                    'producto' => $producto
                ];
            } else {
                error_log("Error al cargar producto ID: " . $idProducto);
            }
        }
        error_log("Total productos procesados: " . count($productos));
    } else {
        error_log("No se encontró compra activa");
    }
} catch (Exception $e) {
    error_log("Error en carrito.php: " . $e->getMessage());
}
?>

<html>
<head>
    <title>Mi Carrito</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php include_once '../Estructura/cabecera.php'; ?>

    <div class="container py-4">
        <div class="row mb-4">
            <div class="col">
                <h2><i class="fas fa-shopping-cart me-2"></i>Mi Carrito</h2>
            </div>
            <div class="col-auto">
                <a href="tienda.php" class="btn btn-primary">
                    <i class="fas fa-store me-2"></i>Volver a la Tienda
                </a>
            </div>
        </div>

        <div id="contenidoCarrito">
            <?php if (isset($compraActiva) && !empty($productos)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th>Precio</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $total = 0;
                            foreach ($productos as $item): 
                                $subtotal = $item['producto']->getPrecio() * $item['item']->getCiCantidad();
                                $total += $subtotal;
                            ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="../img/productos/<?php echo $item['producto']->getImagen(); ?>" 
                                                 class="img-thumbnail me-2" 
                                                 style="width: 50px; height: 50px; object-fit: cover;"
                                                 alt="<?php echo $item['producto']->getProNombre(); ?>">
                                            <?php echo $item['producto']->getProNombre(); ?>
                                        </div>
                                    </td>
                                    <td>$<?php echo number_format($item['producto']->getPrecio(), 2); ?></td>
                                    <td><?php echo $item['item']->getCiCantidad(); ?></td>
                                    <td>$<?php echo number_format($subtotal, 2); ?></td>
                                    <td>
                                        <button class="btn btn-danger btn-sm" 
                                                onclick="eliminarDelCarrito(<?php echo $item['item']->getID(); ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                <td colspan="2"><strong>$<?php echo number_format($total, 2); ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Botones de acción -->
                <div class="row mt-4">
                    <div class="col">
                        <button class="btn btn-success me-2" onclick="aceptarCompra(<?php echo $compraActiva->getIdcompra(); ?>)">
                            <i class="fas fa-check me-2"></i>Aceptar Compra
                        </button>
                        <button class="btn btn-danger" onclick="cancelarCompra(<?php echo $compraActiva->getIdcompra(); ?>)">
                            <i class="fas fa-times me-2"></i>Cancelar Compra
                        </button>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>No tienes productos en tu carrito
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
    function eliminarDelCarrito(idCompraItem) {
        if (confirm('¿Estás seguro de eliminar este producto del carrito?')) {
            $.ajax({
                url: '../../Acciones/producto/eliminarProdCarrito.php',
                type: 'POST',
                dataType: 'json',
                data: { idcompraitem: idCompraItem },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Error al eliminar el producto: ' + (response.message || 'Error desconocido'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error en la petición:', error);
                    console.error('Respuesta del servidor:', xhr.responseText);
                    alert('Error al procesar la solicitud. Por favor, intente nuevamente.');
                }
            });
        }
    }

    function aceptarCompra(idCompra) {
        if (confirm('¿Estás seguro de aceptar la compra? Una vez aceptada, será enviada a revisión.')) {
            $.ajax({
                type: 'POST',
                url: '../../Acciones/compra/ejecutarCompraCarrito.php',
                data: { idcompra: idCompra },
                dataType: 'json',
                success: function(response) {
                    console.log('Respuesta:', response); // Para debug
                    if (response.respuesta) {
                        alert('Compra aceptada correctamente');
                        window.location.href = 'listaCompras.php';
                    } else {
                        alert('Error al aceptar la compra: ' + (response.mensaje || 'Error desconocido'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error en la petición:', error);
                    console.error('Respuesta del servidor:', xhr.responseText);
                    alert('Error al procesar la solicitud. Por favor, intente nuevamente.');
                }
            });
        }
    }

    function cancelarCompra(idCompra) {
        if (confirm('¿Estás seguro de cancelar la compra? Esta acción no se puede deshacer.')) {
            $.ajax({
                type: 'POST',
                url: '../../Acciones/compra/cancelarCompra.php',
                data: { idcompra: idCompra },
                dataType: 'json',
                success: function(response) {
                    if (response.exito) {
                        alert('Compra cancelada correctamente');
                        window.location.href = 'listaCompras.php';
                    } else {
                        alert('Error al cancelar la compra: ' + response.mensaje);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error en la petición:', error);
                    console.error('Respuesta del servidor:', xhr.responseText);
                    alert('Error al procesar la solicitud');
                }
            });
        }
    }
    </script>

    <?php include_once '../Estructura/pie.php'; ?>
</body>
</html>