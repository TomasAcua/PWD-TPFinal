/*################################# CARGAR COMPRAS #################################*/

$(document).ready(function() {
    console.log("Documento listo, iniciando carga de compras...");
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
            if (Array.isArray(response)) {
                armarTabla(response);
            } else if (response.error) {
                $('#mensaje').html(`
                    <div class="alert alert-danger">
                        Error del servidor: ${response.mensaje}
                    </div>
                `);
            } else {
                $('#mensaje').html(`
                    <div class="alert alert-danger">
                        Respuesta inesperada del servidor
                    </div>
                `);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error en la petición:');
            console.error('Status:', status);
            console.error('Error:', error);
            console.error('Respuesta:', xhr.responseText);
            
            let mensaje = 'Error al cargar las compras';
            try {
                const response = JSON.parse(xhr.responseText);
                if (response.mensaje) {
                    mensaje = response.mensaje;
                }
            } catch(e) {
                console.error('Respuesta no es JSON válido:', xhr.responseText);
                mensaje += ': ' + error;
            }
            
            $('#mensaje').html(`
                <div class="alert alert-danger">
                    ${mensaje}
                </div>
            `);
        }
    });
}

function armarTabla(compras) {
    console.log("Armando tabla con datos:", compras);
    $('#tablaCompras > tbody').empty();
    
    if (!Array.isArray(compras) || compras.length === 0) {
        console.log("No hay compras para mostrar");
        $('#tablaCompras > tbody').append(`
            <tr>
                <td colspan="6" class="text-center">No hay compras registradas</td>
            </tr>
        `);
        return;
    }
    
    compras.forEach(function(compra) {
        console.log("Procesando compra:", compra);
        let estadoVista = getEstadoBadge(compra.estado);
        let botones = getBotonesAccion(compra);
        
        $('#tablaCompras > tbody').append(`
            <tr>
                <td>${compra.idcompra}</td>
                <td>
                    <button class="btn btn-info btn-sm verProductos" 
                            data-id="${compra.idcompra}" 
                            data-usuario="${compra.usnombre}">
                        <i class="fas fa-eye me-2"></i>Ver productos
                    </button>
                </td>
                <td>${getEstadoBadge(compra.estado)}</td>
                <td>${compra.cofecha}</td>
                <td>
                    ${compra.estado === 'iniciada' ? 
                        `<button class="btn btn-danger btn-sm cancelarCompra" 
                                data-id="${compra.idcompra}">
                            <i class="fas fa-times me-2"></i>Cancelar
                        </button>` : 
                        ''
                    }
                </td>
            </tr>
        `);
    });
}

function getEstadoBadge(estado) {
    const badges = {
        'iniciada': 'bg-primary',
        'aceptada': 'bg-success',
        'enviada': 'bg-info',
        'cancelada': 'bg-danger'
    };
    
    return `<span class="badge ${badges[estado.toLowerCase()] || 'bg-secondary'}">${estado}</span>`;
}

/*################################# VER PRODUCTOS DE COMPRA #################################*/

$(document).on('click', '.verProductos', function () {

    var fila = $(this).closest('tr');
    var idcompra = fila[0].children[1].innerHTML;
    var pronombre = fila[0].children[2].innerHTML;

    $.ajax({
        type: "POST",
        url: '../Acciones/producto/listadoProds.php',
        data: { idcompra: idcompra },
        success: function (response) {
            arreglo = [];
            $.each($.parseJSON(response), function (index, productoActual) {
                
                    arreglo.push(productoActual);
                
            });
            var dialog = bootbox.dialog({
                message: '<div class="text-center"><i class="fa fa-spin fa-spinner me-2"></i>Listando Productos...</div>',
                closeButton: false
            });
            dialog.init(function () {
                setTimeout(function () {
                    listaProductos(arreglo, pronombre);
                    bootbox.hideAll();
                }, 1000);
            });
        }
    });

});

function listaProductos(arreglo, nombre) {
    document.getElementById('oculto').classList.remove('d-none');
    $('#usnombre').append('<i class="fa-regular fa-rectangle-list me-2"></i>Lista Productos Compra de <b><u>' + nombre + '</u></b>');

    $.each(arreglo, function (index, producto) {
        $('#listaProductos').append('<li class="list-group-item d-flex justify-content-between align-items-start"><div class="row g-0"><div class="col-md-4"><img src="./img/productos/'+producto.imagen+'"class="img-fluid rounded-start" alt="..."></div><div class="col-md-8"><div class="card-body"><h5 class="card-title">' + producto.pronombre + '</h5><p class="card-text">' + producto.prodetalle + '</p><p class="card-text"><small class="text-muted">$ ' + producto.precio + '</small></p></div></div></div><h5><span class="badge text-bg-warning rounded-pill">Cantidad: ' + producto.procantstock + '</span></h5></li>');
    });
};

//CIERRA LA LISTA
$(document).on('click', '#cerrar', function () {
    $('#usnombre').empty();
    $("#listaProductos").empty();
    document.getElementById('oculto').classList.add('d-none');
});

/*################################# CAMBIAR ESTADO COMPRA #################################*/

function cancelarCompra(idcompraestadotipo,idboton,idcompraestado) {

    bootbox.confirm({
        title: "Cancelar Compra",
        closeButton: false,
        message: "¿Est&aacute seguro de que quiere cancelar esta compra? No podr&aacute recuperar el listado de productos que hay en ella.",
        buttons: {
            cancel: {
                className: 'btn btn-outline-primary',
                label: 'Volver'
            },
            confirm: {
                className: 'btn btn-outline-danger',
                label: 'Cancelar Compra'
            }
        },
        callback: function (result) {
            if (result) {
                $.ajax({
                    type: "POST",
                    url: '../Acciones/compra/cancelarCompra.php',
                    data: { idcompraestado: idcompraestado, idcompra: idboton, idcompraestadotipo:idcompraestadotipo},
                    success: function (response) {
                        console.log(response);
                        var response = jQuery.parseJSON(response);
                        if (response) {
                            // CARTEL LIBRERIA, ESPERA 1 SEG Y LUEGO HACE EL RELOAD
                            var dialog = bootbox.dialog({
                                message: '<div class="text-center"><i class="fa fa-spin fa-spinner me-2"></i>Cancelando Compra...</div>',
                                closeButton: false
                            });
                            dialog.init(function () {
                                setTimeout(function () {
                                    cargarCompras();
                                    bootbox.hideAll();
                                }, 1000);
                            });
                        } else {
                            // ALERT LIBRERIA
                            bootbox.alert({
                                message: "No se pudo cancelar la compra!",
                                size: 'small',
                                closeButton: false,
                            });
                        }
                    }
                });
            }
        }
    });

    
}