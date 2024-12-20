/*################################# FORMULARIO DE REGISTRO #################################*/
$('#modificar').submit(function (e) {
    e.preventDefault();
    e.stopImmediatePropagation();

    var inputs = $('#modificar :input');
    var pass1 = hex_md5(inputs[3].value);
    var pass2 = hex_md5(inputs[4].value);

    if (pass1 === pass2) {
        arreglo = {
            usnombre: inputs[1].value,
            usmail: inputs[2].value,
            uspass: pass1,
            usdeshabilitado: null,
            idusuario: inputs[5].value
        }

        $.ajax({
            type: "POST",
            url: '/TPFinal/Acciones/usuario/actualizar.php',
            data: arreglo,
            success: function (response) {
                console.log(response);
                var response = jQuery.parseJSON(response);
                if (response) {
                    var dialog = bootbox.dialog({
                        message: '<div class="text-center"><i class="fa fa-spin fa-spinner me-2"></i>Actualizando Perfil ...</div>',
                        closeButton: false
                    });
                    dialog.init(function () {
                        setTimeout(function () {
                            window.location.href = "/TPFinal/Vista/login.php";
                        }, 1000);
                    });
                } else {
                    bootbox.alert({
                        message: "No se pudo completar la actualización!",
                        size: 'small',
                        closeButton: false,
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                bootbox.alert({
                    message: "Error al actualizar el perfil: " + error,
                    size: 'small',
                    closeButton: false,
                });
            }
        });
    } else {
        bootbox.alert({
            message: "Las contraseñas deben ser iguales!",
            size: 'small',
            closeButton: false,
        });
    }
});