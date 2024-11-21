<?php
$Titulo = "Tabla Compras";
include_once '../Estructura/cabecera.php';

if (!$sesion->verificarPermiso('deposito/tablacompras.php')) {
    $mensaje = "No tiene permiso para acceder a este sitio.";
    echo "<script> window.location.href='/TPFinal/Vista/index.php?mensaje=" . urlencode($mensaje) . "'</script>";
    exit;
}
?>

<div class="container mt-4">
    <h2 class="mb-4">Gestión de Compras</h2>
    <div id="mensaje"></div>
    <table id="tablaCompras" class="table table-hover">
        <thead>
            <tr>
                <th>ID Compra</th>
                <th>Usuario</th>
                <th>Productos</th>
                <th>Estado</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

<!-- Asegurarnos que jQuery esté cargado primero -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Nuestro script con debug -->
<script>
console.log('Documento cargado');

$(document).ready(function() {
    console.log('jQuery listo');
    cargarCompras();
});

function cargarCompras() {
    console.log('Iniciando cargarCompras()');
    const url = '/TPFinal/Acciones/compra/listadoCompras.php';
    console.log('URL:', url);
    
    $.ajax({
        type: "POST",
        url: url,
        dataType: 'json',
        success: function(response) {
            console.log('Respuesta recibida:', response);
            armarTabla(response);
        },
        error: function(xhr, status, error) {
            console.error('Error en la petición:');
            console.error('Status:', status);
            console.error('Error:', error);
            console.error('Respuesta:', xhr.responseText);
            $('#mensaje').html(`
                <div class="alert alert-danger">
                    Error al cargar las compras: ${error}
                </div>
            `);
        }
    });
}

function armarTabla(compras) {
    console.log('Armando tabla con:', compras);
    $('#tablaCompras > tbody').empty();
    
    if (!Array.isArray(compras) || compras.length === 0) {
        $('#tablaCompras > tbody').append(`
            <tr>
                <td colspan="6" class="text-center">No hay compras registradas</td>
            </tr>
        `);
        return;
    }
    
    compras.forEach(function(compra) {
        let estadoVista = getEstadoBadge(compra.estado);
        let botones = getBotonesAccion(compra);
        
        $('#tablaCompras > tbody').append(`
            <tr>
                <td>${compra.idcompra}</td>
                <td>${compra.usnombre}</td>
                <td>
                    <button class="btn btn-info btn-sm" onclick="verProductos(${compra.idcompra})">
                        <i class="fas fa-list"></i> Ver
                    </button>
                </td>
                <td>${estadoVista}</td>
                <td>${compra.cofecha}</td>
                <td>${botones}</td>
            </tr>
        `);
    });
}

function getEstadoBadge(estado) {
    const badges = {
        'carrito': '<span class="badge bg-primary">En carrito</span>',
        'iniciada': '<span class="badge bg-warning">Iniciada</span>',
        'aceptada': '<span class="badge bg-success">Aceptada</span>',
        'enviada': '<span class="badge bg-info">Enviada</span>',
        'cancelada': '<span class="badge bg-danger">Cancelada</span>'
    };
    return badges[estado?.toLowerCase()] || `<span class="badge bg-secondary">${estado || 'Sin estado'}</span>`;
}

function getBotonesAccion(compra) {
    // Si la compra está enviada o cancelada, no mostramos botones de acción
    if (['enviada', 'cancelada'].includes(compra.estado?.toLowerCase())) {
        return `<span class="badge bg-secondary">No disponible</span>`;
    }

    return `
        <button class="btn btn-success btn-sm" onclick="aceptarCompra(${compra.idcompra})" title="Aceptar compra">
            <i class="fas fa-check"></i>
        </button>
        <button class="btn btn-danger btn-sm" onclick="cancelarCompra(${compra.idcompra})" title="Cancelar compra">
            <i class="fas fa-times"></i>
        </button>
    `;
}

function verProductos(idcompra) {
    console.log('Viendo productos de compra:', idcompra);
    $.ajax({
        type: "POST",
        url: '/TPFinal/Acciones/compra/listarProductosCompra.php',
        data: { idcompra: idcompra },
        dataType: 'json',
        success: function(response) {
            mostrarModalProductos(response);
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            alert('Error al cargar los productos');
        }
    });
}

function mostrarModalProductos(productos) {
    let contenido = `
        <table class="table">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Stock Disponible</th>
                </tr>
            </thead>
            <tbody>
    `;
    
    productos.forEach(prod => {
        contenido += `
            <tr>
                <td>${prod.pronombre}</td>
                <td>${prod.cantidad}</td>
                <td>${prod.stock_actual}</td>
            </tr>
        `;
    });
    
    contenido += '</tbody></table>';
    
    bootbox.dialog({
        title: 'Productos de la Compra',
        message: contenido,
        size: 'large'
    });
}

function aceptarCompra(idcompra) {
    // Primero verificamos el stock
    $.ajax({
        type: "POST",
        url: '/TPFinal/Acciones/compra/verificarStock.php',
        data: { idcompra: idcompra },
        dataType: 'json',
        success: function(response) {
            if (response.exito) {
                confirmarAceptacion(idcompra);
            } else {
                let mensaje = "No hay suficiente stock para los siguientes productos:<br><ul>";
                response.productosInsuficientes.forEach(function(prod) {
                    mensaje += `<li>${prod.producto}: Solicitado: ${prod.solicitado}, Disponible: ${prod.disponible}</li>`;
                });
                mensaje += "</ul>";
                
                bootbox.alert({
                    title: "Stock Insuficiente",
                    message: mensaje
                });
            }
        },
        error: function(xhr, status, error) {
            bootbox.alert("Error al verificar stock: " + error);
        }
    });
}

function confirmarAceptacion(idcompra) {
    bootbox.confirm({
        title: "Confirmar Aceptación",
        message: "¿Está seguro de aceptar esta compra? Se actualizará el stock de los productos.",
        buttons: {
            confirm: {
                label: 'Sí',
                className: 'btn-success'
            },
            cancel: {
                label: 'No',
                className: 'btn-secondary'
            }
        },
        callback: function(result) {
            if (result) {
                ejecutarAceptacion(idcompra);
            }
        }
    });
}

function ejecutarAceptacion(idcompra) {
    $.ajax({
        type: "POST",
        url: '/TPFinal/Acciones/compra/aceptarCompra.php',
        data: { idcompra: idcompra },
        dataType: 'json',
        success: function(response) {
            if (response.exito) {
                bootbox.alert("Compra aceptada exitosamente", function() {
                    cargarCompras(); // Recargar tabla
                });
            } else {
                bootbox.alert("Error: " + response.mensaje);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            alert('Error al aceptar la compra');
        }
    });
}

function cancelarCompra(idcompra) {
    bootbox.confirm({
        title: 'Confirmar Cancelación',
        message: '¿Está seguro de cancelar esta compra?',
        buttons: {
            confirm: {
                label: 'Sí',
                className: 'btn-danger'
            },
            cancel: {
                label: 'No',
                className: 'btn-secondary'
            }
        },
        callback: function(result) {
            if (result) {
                ejecutarCancelacion(idcompra);
            }
        }
    });
}

function ejecutarCancelacion(idcompra) {
    $.ajax({
        type: "POST",
        url: '/TPFinal/Acciones/compra/cancelarCompra.php',
        data: { idcompra: idcompra },
        dataType: 'json',
        success: function(response) {
            if (response.exito) {
                bootbox.alert("Compra cancelada exitosamente", function() {
                    cargarCompras(); // Recargar tabla
                });
            } else {
                bootbox.alert("Error: " + response.mensaje);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            alert('Error al cancelar la compra');
        }
    });
}
</script>

<?php include_once '../Estructura/pie.php'; ?>