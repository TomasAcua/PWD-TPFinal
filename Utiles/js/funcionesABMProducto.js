/*################################################## CARGAR PRODUCTOS ##################################################*/
$(document).ready(function() {
    console.log("Iniciando carga de productos...");
    cargarProductos();
});

function cargarProductos() {
    console.log("Intentando cargar productos...");
    $.ajax({
        type: "POST",
        url: '/TPFinal/Acciones/producto/listarProductos.php',
        dataType: 'json',
        success: function(response) {
            console.log("Respuesta del servidor:", response);
            actualizarTablaProductos(response);
        },
        error: function(xhr, status, error) {
            console.error("Error al cargar productos:");
            console.error("Status:", status);
            console.error("Error:", error);
            console.error("Respuesta:", xhr.responseText);
        }
    });
}

function actualizarTablaProductos(productos) {
    console.log("Actualizando tabla con productos:", productos);
    let tbody = $('#tablaProductos tbody');
    tbody.empty();
    
    productos.forEach(function(producto) {
        let deshabilitadoText = producto.prodeshabilitado ? 'Sí' : 'No';
        let botonEliminar = producto.prodeshabilitado ? 
            `<button class="btn btn-success btn-habilitar" onclick="habilitarProducto(${producto.idproducto})">
                <i class="fas fa-check"></i> Habilitar
            </button>` :
            `<button class="btn btn-danger btn-eliminar" onclick="eliminarProducto(${producto.idproducto})">
                <i class="fas fa-trash"></i> Eliminar
            </button>`;

        let fila = `
            <tr class="${producto.prodeshabilitado ? 'table-secondary' : ''}">
                <td>${producto.idproducto}</td>
                <td>${producto.pronombre}</td>
                <td>${producto.prodetalle}</td>
                <td>${producto.procantstock}</td>
                <td>$${producto.precio}</td>
                <td><img src="/TPFinal/Vista/img/productos/${producto.imagen}" alt="Imagen producto" style="max-width: 50px;"></td>
                <td>${deshabilitadoText}</td>
                <td>
                    <button class="btn btn-primary btn-editar" onclick="editarProducto(${producto.idproducto})">
                        <i class="fas fa-edit"></i> Editar
                    </button>
                    ${botonEliminar}
                </td>
            </tr>`;
        tbody.append(fila);
    });
}

/*################################################## AGREGAR PRODUCTO ##################################################*/
$('#agregar').submit(function (e) {
    e.preventDefault();
    formData = new FormData(this);
    $.ajax({
        type: "POST",
        url: '/TPFinal/Acciones/producto/altaProd.php',
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            console.log(response);
            var response = jQuery.parseJSON(response);
            if (response) {
                // CARTEL LIBRERIA, ESPERA 1SEG Y REFRESCA LA TABLA
                var dialog = bootbox.dialog({
                    message: '<div class="text-center"><i class="fa fa-spin fa-spinner me-2"></i>Cargando Producto...</div>',
                    closeButton: false
                });
                dialog.init(function () {
                    setTimeout(function () {
                        $('#agregar-modal-producto').modal('hide'); //OCULTAMOS EL MODAL
                        cargarProductos();
                        bootbox.hideAll();
                    }, 1000);
                });
            } else {
                // ALERT LIBRERIA
                bootbox.alert({
                    message: "No se pudo hacer el alta!",
                    size: 'small',
                    closeButton: false,
                });
            }
        }
    });
});

/*################################################## EDITAR PRODUCTO ##################################################*/

$(document).on('click', '.editarButton', function () { //MUESTRA EL FORMULARIO Y PRECARGA LOS DATOS
    var fila = $(this).closest('tr');

    var idproducto = fila[0].children[0].innerHTML;
    var pronombre = fila[0].children[1].innerHTML;
    var prodetalle = fila[0].children[2].innerHTML;
    var procantstock = fila[0].children[3].innerHTML;
    var precio = fila[0].children[4].innerHTML;
    var prodeshabilitado = fila[0].children[6].innerHTML;

    //GUARDAMOS EL NOMBRE DEL JPG PARA REEMPLAZARLO POR UNO NUEVO, SI ES QUE SE DECIDE REEMPLAZARSE.
    var images = $(this).closest('tr').children('td').children('img').attr('src');
    url = images.replace('../img/productos/', '');
    sessionStorage.setItem('url', url);
    // EL ID PARA SABER DE QUE PRODUCTO ACTUALIZAREMOS LA IMAGEN
    sessionStorage.setItem('id', idproducto);

    var form = document.getElementById('editarForm');
    var inputs = form.getElementsByTagName('input');

    inputs[0].value = idproducto;
    inputs[1].value = prodeshabilitado;
    inputs[2].value = pronombre;
    inputs[3].value = prodetalle;
    inputs[4].value = procantstock;
    inputs[5].value = precio;
    inputs[6].value = url;
});

//ENVIA LOS DATOS
$('#editarForm').submit(function (e) {
    e.preventDefault();

    $.ajax({
        type: "POST",
        url: '/TPFinal/Acciones/producto/editarProd.php',
        data: $(this).serialize(),
        success: function (response) {
            var response = jQuery.parseJSON(response);
            if (response) {
                // CARTEL LIBRERIA, ESPERA 1 SEG Y REFRESCA LA TABLA
                var dialog = bootbox.dialog({
                    message: '<div class="text-center"><i class="fa fa-spin fa-spinner me-2"></i>Editando Producto...</div>',
                    closeButton: false
                });
                dialog.init(function () {
                    setTimeout(function () {
                        $('#editar-modal-producto').modal('hide'); //OCULTAMOS EL MODAL
                        cargarProductos();
                        bootbox.hideAll();
                    }, 1000);
                });
            } else {
                // ALERT LIBRERIA
                bootbox.alert({
                    message: "No se pudo completar la modificación!",
                    size: 'small',
                    closeButton: false,
                });
            }
        }
    });
});

/*################################################## EDITAR IMAGEN PRODUCTO ##################################################*/
$('.editarImagenButton').click(function () {
    url = sessionStorage.getItem('url');
    id = sessionStorage.getItem('id');
    var form = document.getElementById('editar-modal-imagen');
    var inputs = form.getElementsByTagName('input');

    inputs[0].value = url;
    inputs[1].value = id;
});

// SUBMIT FORMULARIO EDITAR IMAGEN
$('#editarImagen').submit(function (e) {
    e.preventDefault();
    formData = new FormData(this);
    $.ajax({
        type: "POST",
        url: '/TPFinal/Acciones/producto/editarImagen.php',
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            console.log(response);
            var response = jQuery.parseJSON(response);
            if (response) {
                // CARTEL LIBRERIA, ESPERA 1SEG Y REFRESCA LA TABLA
                var dialog = bootbox.dialog({
                    message: '<div class="text-center"><i class="fa fa-spin fa-spinner me-2"></i>Editando Imagen...</div>',
                    closeButton: false
                });
                dialog.init(function () {
                    setTimeout(function () {
                        $('#editar-modal-imagen').modal('hide'); //OCULTAMOS EL MODAL
                        cargarProductos();
                        bootbox.hideAll();
                    }, 1000);
                });
            } else {
                // ALERT LIBRERIA
                bootbox.alert({
                    message: "No se pudo hacer la modificación!",
                    size: 'small',
                    closeButton: false,
                });
            }
        }
    });
});

/*################################################## ELIMINAR PRODUCTO ##################################################*/

$(document).on('click', '.eliminar', function () {

    var fila = $(this).closest('tr');
    var idproducto = fila[0].children[0].innerHTML;
    var pronombre = fila[0].children[1].innerHTML;

    // CARTEL LIBRERIA
    bootbox.confirm({
        title: "Eliminar Producto?",
        closeButton: false,
        message: "Estas seguro que quieres eliminar a <b>" + pronombre + "</b> con ID: <b>" + idproducto + '</b>',
        buttons: {
            cancel: {
                className: 'btn btn-outline-danger',
                label: '<i class="fa fa-times"></i> Cancelar'
            },
            confirm: {
                className: 'btn btn-outline-success',
                label: '<i class="fa fa-check"></i> Confirmar'
            }
        },
        callback: function (result) {
            if (result) {
                eliminar(idproducto);
            }
        }
    });
});

function eliminar(idproducto) {

    $.ajax({
        type: "POST",
        url: '/TPFinal/Acciones/producto/eliminarProducto.php',
        data: { idproducto: idproducto },
        success: function (response) {
            var response = jQuery.parseJSON(response);
            if (response) {
                // CARTEL LIBRERIA, ESPERA 1SEG Y REFRESCA LA TABLA
                var dialog = bootbox.dialog({
                    message: '<div class="text-center"><i class="fa fa-spin fa-spinner me-2"></i>Borrando Producto...</div>',
                    closeButton: false
                });
                dialog.init(function () {
                    setTimeout(function () {
                        cargarProductos();
                        bootbox.hideAll();
                    }, 1000);
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
};


/*################################################## DESHABILITAR PRODUCTO ##################################################*/

$(document).on('click', '.deshabilitar', function () {

    var fila = $(this).closest('tr');

    var idproducto = fila[0].children[0].innerHTML;
    var pronombre = fila[0].children[1].innerHTML;

    // CARTEL LIBRERIA
    bootbox.confirm({
        closeButton: false,
        title: "DESHABILITAR PRODUCTO?",
        message: "Estas seguro que quieres DESHABILITAR a <b>" + pronombre + "</b> con ID:<b>" + idproducto + "</b>",
        buttons: {
            cancel: {
                className: 'btn btn-outline-danger',
                label: '<i class="fa fa-times"></i> Cancelar'
            },
            confirm: {
                className: 'btn btn-outline-secondary',
                label: '<i class="fa-solid fa-ban"></i> Deshabilitar'
            }
        },
        callback: function (result) {
            if (result) {
                deshabilitar(idproducto);
            }
        }
    });
});

function deshabilitar(idproducto) {
    $.ajax({
        type: "POST",
        url: '/TPFinal/Acciones/producto/deshabilitarProducto.php',
        data: { idproducto: idproducto, accion: 'deshabilitar' },
        success: function (response) {
            var response = jQuery.parseJSON(response);
            if (response) {
                // CARTEL LIBRERIA, ESPERA 1SEG Y REFRESCA LA TABLA
                var dialog = bootbox.dialog({
                    message: '<div class="text-center"><i class="fa fa-spin fa-spinner me-2"></i>Deshabilitando Producto...</div>',
                    closeButton: false
                });
                dialog.init(function () {
                    setTimeout(function () {
                        cargarProductos();
                        bootbox.hideAll();
                    }, 1000);
                });
            } else {
                // ALERT LIBRERIA
                bootbox.alert({
                    message: "No se pudo deshabilitar el producto!",
                    size: 'small',
                    closeButton: false,
                });
            }
        }
    });
};

/*################################################## HABILITAR PRODUCTO ##################################################*/

$(document).on('click', '.habilitar', function () {

    var fila = $(this).closest('tr');

    var idproducto = fila[0].children[0].innerHTML;
    var pronombre = fila[0].children[1].innerHTML;

    // CARTEL LIBRERIA
    bootbox.confirm({
        title: "HABILITAR PRODUCTO?",
        closeButton: false,
        message: "Estas seguro que quieres HABILITAR a <b>" + pronombre + "</b> con ID: <b>" + idproducto + '</b>',
        buttons: {
            cancel: {
                className: 'btn btn-outline-danger',
                label: '<i class="fa fa-times"></i> Cancelar'
            },
            confirm: {
                className: 'btn btn-outline-success',
                label: '<i class="fa-solid fa-square-check"></i> Habilitar'
            }
        },
        callback: function (result) {
            if (result) {
                habilitar(idproducto);
            }
        }
    });
});

function habilitar(idproducto) {

    $.ajax({
        type: "POST",
        url: '/TPFinal/Acciones/producto/deshabilitarProducto.php',
        data: { idproducto: idproducto, accion: 'habilitar' },
        success: function (response) {
            var response = jQuery.parseJSON(response);
            if (response) {
                // CARTEL LIBRERIA, ESPERA 1SEG Y REFRESCA LA TABLA
                var dialog = bootbox.dialog({
                    message: '<div class="text-center"><i class="fa fa-spin fa-spinner me-2"></i>Habilitando Producto...</div>',
                    closeButton: false
                });
                dialog.init(function () {
                    setTimeout(function () {
                        cargarProductos();
                        bootbox.hideAll();
                    }, 1000);
                });
            } else {
                // ALERT LIBRERIA
                bootbox.alert({
                    message: "No se pudo habilitar el producto!",
                    size: 'small',
                    closeButton: false,
                });
            }
        }
    });
};

function editarProducto(idproducto) {
    console.log("Editando producto:", idproducto);
    $.ajax({
        type: "POST",
        url: '/TPFinal/Acciones/producto/listarProductos.php',
        data: { idproducto: idproducto },
        dataType: 'json',
        success: function(response) {
            console.log("Datos del producto a editar:", response);
            if (response && response.length > 0) {
                let producto = response[0];
                // Llenar el modal con los datos del producto
                $('#idproducto').val(producto.idproducto);
                $('#edit_pronombre').val(producto.pronombre);
                $('#edit_prodetalle').val(producto.prodetalle);
                $('#edit_procantstock').val(producto.procantstock);
                $('#edit_precio').val(producto.precio);
                
                // Mostrar el modal
                $('#modal-editar-producto').modal('show');
            }
        },
        error: function(xhr, status, error) {
            console.error("Error al obtener datos del producto:", error);
        }
    });
}

function eliminarProducto(idproducto) {
    console.log("Deshabilitando producto:", idproducto);
    bootbox.confirm({
        title: "Deshabilitar Producto",
        message: "¿Está seguro que desea deshabilitar este producto? No podrá ser comprado pero se mantendrá el historial de compras.",
        buttons: {
            cancel: {
                label: '<i class="fa fa-times"></i> Cancelar',
                className: 'btn-secondary'
            },
            confirm: {
                label: '<i class="fa fa-ban"></i> Deshabilitar',
                className: 'btn-danger'
            }
        },
        callback: function(result) {
            if (result) {
                $.ajax({
                    type: "POST",
                    url: '/TPFinal/Acciones/producto/eliminarProducto.php',
                    data: { 
                        idproducto: idproducto,
                        action: 'eliminar'
                    },
                    success: function(response) {
                        console.log("Respuesta del servidor:", response);
                        try {
                            if (response.success) {
                                bootbox.alert({
                                    message: "Producto deshabilitado exitosamente",
                                    callback: function() {
                                        cargarProductos();
                                    }
                                });
                            } else {
                                bootbox.alert("Error al deshabilitar el producto");
                            }
                        } catch(e) {
                            console.error("Error al procesar respuesta:", e);
                            console.error("Respuesta recibida:", response);
                            bootbox.alert("Error al procesar la respuesta del servidor");
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error en la petición:", {
                            status: status,
                            error: error,
                            response: xhr.responseText
                        });
                        bootbox.alert("Error en la comunicación con el servidor");
                    }
                });
            }
        }
    });
}

// Función para guardar cambios al editar
$('#form-editar-producto').on('submit', function(e) {
    e.preventDefault();
    let formData = new FormData(this);
    
    $.ajax({
        type: "POST",
        url: '/TPFinal/Acciones/producto/editarProd.php',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                $('#modal-editar-producto').modal('hide');
                bootbox.alert({
                    message: "Producto actualizado exitosamente",
                    callback: function() {
                        cargarProductos(); // Recargar la tabla
                    }
                });
            } else {
                bootbox.alert("Error al actualizar el producto");
            }
        }
    });
});

// Función para agregar nuevo producto
$('#form-add-producto').on('submit', function(e) {
    e.preventDefault();
    console.log("Enviando formulario de nuevo producto");
    let formData = new FormData(this);
    
    $.ajax({
        type: "POST",
        url: '/TPFinal/Acciones/producto/altaProd.php',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            console.log("Respuesta del servidor:", response);
            if (response) {
                $('#modal-add-producto').modal('hide');
                bootbox.alert({
                    message: "Producto agregado exitosamente",
                    callback: function() {
                        cargarProductos();
                        $('#form-add-producto')[0].reset();
                    }
                });
            } else {
                bootbox.alert("Error al agregar el producto: " + response.mensaje);
            }
        },
        error: function(xhr, status, error) {
            console.error("Error en la petición:", error);
            console.error("Respuesta del servidor:", xhr.responseText);
            bootbox.alert("Error al procesar la solicitud");
        }
    });
});

// Agregar la nueva función para habilitar productos
function habilitarProducto(idproducto) {
    console.log("Habilitando producto:", idproducto);
    bootbox.confirm({
        title: "Habilitar Producto",
        message: "¿Está seguro que desea habilitar este producto?",
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
            if(result) {
                $.ajax({
                    type: "POST",
                    url: "/TPFinal/Acciones/producto/habilitarProducto.php",
                    data: { idproducto: idproducto },
                    dataType: 'json',
                    success: function(response) {
                        console.log("Respuesta del servidor:", response);
                        if(response.success) {
                            bootbox.alert("Producto habilitado exitosamente", function() {
                                cargarProductos();
                            });
                        } else {
                            bootbox.alert("Error al habilitar el producto: " + (response.message || "Error desconocido"));
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error en la petición:", {
                            status: status,
                            error: error,
                            response: xhr.responseText
                        });
                        bootbox.alert("Error en la comunicación con el servidor");
                    }
                });
            }
        }
    });
}