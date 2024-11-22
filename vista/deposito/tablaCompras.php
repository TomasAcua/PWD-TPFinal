<?php
$Titulo = "Tabla Compras";
include_once '../Estructura/cabecera.php';

if (!$sesion->verificarPermiso('deposito/tablacompras.php')) {
    $mensaje = "No tiene permiso para acceder a este sitio.";
    echo "<script> window.location.href='/TPFinal/Vista/index.php?mensaje=" . urlencode($mensaje) . "'</script>";
    exit;
}
?>

<head>
   
</head>

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

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap Bundle con Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Bootbox -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.5.2/bootbox.min.js"></script>

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
        let estadoVista = getEstadoBadge(compra.estado || 'sin estado');
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
        'iniciada': '<span class="badge bg-warning">Iniciada</span>',
        'aceptada': '<span class="badge bg-success">Aceptada</span>',
        'enviada': '<span class="badge bg-info">Enviada</span>',
        'cancelada': '<span class="badge bg-danger">Cancelada</span>',
        'sin estado': '<span class="badge bg-secondary">Sin estado</span>'
    };
    return badges[estado.toLowerCase()] || `<span class="badge bg-secondary">${estado}</span>`;
}

function getBotonesAccion(compra) {
    let botones = '';
    const estado = compra.estado?.toLowerCase() || 'sin estado';
    
    console.log('Estado de la compra:', estado);
    
    switch(estado) {
        case 'iniciada':
            botones = `
                <button class="btn btn-success btn-sm" onclick="aceptarCompra(${compra.idcompra})" title="Aceptar compra">
                    <i class="fas fa-check"></i>
                </button>
                <button class="btn btn-danger btn-sm" onclick="cancelarCompra(${compra.idcompra})" title="Cancelar compra">
                    <i class="fas fa-times"></i>
                </button>
            `;
            break;
            
        case 'aceptada':
            botones = `
                <button class="btn btn-info btn-sm" onclick="enviarCompra(${compra.idcompra})" title="Marcar como enviada">
                    <i class="fas fa-truck"></i>
                </button>
                <button class="btn btn-danger btn-sm" onclick="cancelarCompra(${compra.idcompra})" title="Cancelar compra">
                    <i class="fas fa-times"></i>
                </button>
            `;
            break;
            
        case 'enviada':
        case 'cancelada':
            botones = `<span class="badge bg-secondary">No disponible</span>`;
            break;
            
        case 'sin estado':
            botones = `<span class="badge bg-warning">Pendiente</span>`;
            break;
            
        default:
            botones = `<span class="badge bg-secondary">Estado desconocido</span>`;
    }
    
    return botones;
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
    bootbox.confirm({
        title: 'Confirmar Aceptación',
        message: '¿Está seguro de aceptar esta compra?',
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
                cambiarEstadoCompra(idcompra, 2); // 2 = aceptada
            }
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
                cambiarEstadoCompra(idcompra, 4); // 4 = cancelada
            }
        }
    });
}

function enviarCompra(idcompra) {
    bootbox.confirm({
        title: 'Confirmar Envío',
        message: '¿Está seguro de marcar esta compra como enviada?',
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
                cambiarEstadoCompra(idcompra, 3); // 3 = enviada
            }
        }
    });
}

function cambiarEstadoCompra(idcompra, idcompraestadotipo) {
    $.ajax({
        type: "POST",
        url: '../../Acciones/compras/modificarEstadoCompra.php',
        data: { 
            idcompra: idcompra,
            idcompraestadotipo: idcompraestadotipo
        },
        dataType: 'json',
        success: function(response) {
            if (response.exito) {
                let mensaje = '';
                switch(idcompraestadotipo) {
                    case 2:
                        mensaje = "Compra aceptada exitosamente";
                        break;
                    case 3:
                        mensaje = "Compra marcada como enviada exitosamente";
                        break;
                    case 4:
                        mensaje = "Compra cancelada exitosamente";
                        break;
                }
                
                bootbox.alert(mensaje, function() {
                    cargarCompras();
                });
            } else {
                bootbox.alert("Error: " + response.mensaje);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error en la petición:', error);
            console.error('Respuesta del servidor:', xhr.responseText);
            bootbox.alert('Error al procesar la solicitud');
        }
    });
}
</script>

<?php include_once '../Estructura/pie.php'; ?>