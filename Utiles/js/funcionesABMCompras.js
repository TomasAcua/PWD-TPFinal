/*################################################## CARGAR COMPRAS ##################################################*/

$(document).ready(function() {
    console.log("Iniciando carga de compras...");
    cargarCompras();
});

function cargarCompras() {
    $.ajax({
        type: "POST",
        url: "../Acciones/compra/listadoCompras.php",
        success: function(response) {
            console.log("Respuesta del servidor:", response);
            try {
                let compras = JSON.parse(response);
                actualizarTablaCompras(compras);
            } catch (e) {
                console.error("Error al parsear respuesta:", e);
                console.log("Respuesta raw:", response);
            }
        },
        error: function(xhr, status, error) {
            console.error("Error al cargar compras:", error);
            console.log("Status:", status);
            console.log("Response:", xhr.responseText);
        }
    });
}

function actualizarTablaCompras(compras) {
    console.log("Actualizando tabla con compras:", compras);
    let tbody = $('#tablaCompras tbody');
    tbody.empty();
    
    compras.forEach(function(compra) {
        let fila = `
            <tr>
                <td>${compra.idcompra}</td>
                <td>${compra.usnombre}</td>
                <td>
                    <button class="btn btn-info btn-ver-productos" data-id="${compra.idcompra}" data-usuario="${compra.usnombre}">
                        <i class="fas fa-eye me-2"></i>Ver Productos
                    </button>
                </td>
                <td>${compra.estado}</td>
                <td>${compra.cofecha}</td>
                <td>
                    <button class="btn btn-success btn-cambiar-estado" data-id="${compra.idcompra}">
                        <i class="fas fa-sync-alt me-2"></i>Cambiar Estado
                    </button>
                </td>
            </tr>`;
        tbody.append(fila);
    });
}

/*################################################## VER PRODUCTOS DE COMPRA ##################################################*/

$(document).on('click', '.verProductos', function () {

    var fila = $(this).closest('tr');
    var idcompra = fila[0].children[1].innerHTML;
    var pronombre = fila[0].children[2].innerHTML;


    $.ajax({
        type: "POST",
        url: '../Acciones/compras/listarProdCompra.php',
        data: { idcompra: idcompra },
        success: function (response) {
            console.log(response);
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
                }, 750);
            });
        }
    });

});

function listaProductos(arreglo, nombre) {
    document.getElementById('oculto').classList.remove('d-none');
    $('#usnombre').append('<i class="fa-regular fa-rectangle-list me-2"></i>Lista Productos Compra de <b><u>' + nombre + '</u></b>');
    $.each(arreglo, function (index, producto) {
        $('#listaProductos').append('<div class="card mb-3"><div class="row g-0"><div class="col-md-4"><img src="./img/productos/'+producto.imagen+'" width="350" class="img-fluid rounded-start" ></div><div class="col-md-8"><div class="card-body"><h5 class="card-title">' + producto.pronombre + '</h5><p class="card-text">' + producto.prodetalle + '</p><p class="card-text"><small class="text-muted">$ ' + producto.precio + '</small></p><h5><span class="badge text-bg-warning rounded-pill">Cantidad: ' + producto.procantstock + '</span></h5></div></div></div></div>');
    });
};

//CIERRA LA LISTA
$(document).on("click", "#cerrar", function () {
  $("#usnombre").empty();
  $("#listaProductos").empty();
  document.getElementById("oculto").classList.add("d-none");
});

/*################################################## CAMBIAR ESTADO COMPRA ##################################################*/

function cambiarEstado(idcompraestadotipo,idcompra,idcompraestado) {  
    $.ajax({
        type: "POST",
        url: '../Acciones/compras/modificarEstadoCompra.php',
        data: { idcompraestado: idcompraestado, idcompra: idcompra, idcompraestadotipo: idcompraestadotipo },
        success: function (response) {
            console.log(response);
            var response = jQuery.parseJSON(response);
            if (response) {
                // CARTEL LIBRERIA, ESPERA 1 SEG Y LUEGO HACE EL RELOAD
                var dialog = bootbox.dialog({
                    message: '<div class="text-center"><i class="fa fa-spin fa-spinner me-2"></i>Modificando Estado Compra...</div>',
                    closeButton: false
                });
                dialog.init(function () {
                    setTimeout(function () {
                        cargarCompras();
                        bootbox.hideAll();
                    }, 750);
                });
            } else {
                // ALERT LIBRERIA
                bootbox.alert({
                    message: "No se pudo eliminar el producto!",
                    size: 'small',
                    closeButton: false,
                });
            }
        }
    });
}