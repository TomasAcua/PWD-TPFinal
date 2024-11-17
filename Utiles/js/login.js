/*################################# FORMULARIO DE INICIO SESIÓN #################################*/
$(document).ajaxError(function(event, jqxhr, settings, error) {
    console.error("Error global Ajax:", {
        url: settings.url,
        type: settings.type,
        error: error,
        status: jqxhr.status,
        response: jqxhr.responseText
    });
});

$("#login").submit(function (e) {
  e.preventDefault();
  e.stopImmediatePropagation();
  
  console.log("Formulario enviado"); // Debug
  
  var username = $("#usnombre").val();
  var password = $("#uspass").val();
  
  if (username && password) {
    var hashedPassword = hex_md5(password);
    console.log("Intentando login con:", {
      username: username,
      passwordHash: hashedPassword
    });
    
    // Verificar que la URL sea correcta
    var baseUrl = window.location.pathname.substring(0, window.location.pathname.indexOf('/Vista/'));
    var loginUrl = baseUrl + '/Acciones/login/ingresar.php';
    
    console.log("URL de login:", loginUrl); // Debug
    
    $.ajax({
      type: "POST",
      url: loginUrl,
      data: {
        usnombre: username,
        uspass: hashedPassword
      },
      dataType: 'json',
      success: function(response) {
        console.log("Respuesta del servidor:", response);
        
        if (response.success) {
          var dialog = bootbox.dialog({
            message: '<div class="text-center"><i class="fa fa-spin fa-spinner me-2"></i>Iniciando...</div>',
            closeButton: false
          });
          dialog.init(function() {
            setTimeout(function() {
              window.location.href = "index.php?mensaje=Sesión iniciada correctamente!";
            }, 750);
          });
        } else {
          bootbox.alert({
            message: response.message || "Error al iniciar sesión",
            size: 'small',
            closeButton: false
          });
        }
      },
      error: function(xhr, status, error) {
        console.error("Error en Ajax:", {
          status: status,
          error: error,
          response: xhr.responseText
        });
        
        let errorMessage = "Error de conexión. Por favor, intente más tarde.";
        
        if (xhr.responseText) {
            try {
                const response = JSON.parse(xhr.responseText);
                if (response.message) {
                    errorMessage = response.message;
                }
            } catch (e) {
                console.error("Error al parsear respuesta:", e);
            }
        }
        
        bootbox.alert({
          message: errorMessage,
          size: 'small',
          closeButton: false
        });
      }
    });
  } else {
    bootbox.alert({
      message: "Por favor complete todos los campos",
      size: 'small',
      closeButton: false
    });
  }
});

// Verificar que jQuery y el evento submit estén funcionando
$(document).ready(function() {
  console.log("Document ready");
  console.log("jQuery version:", $.fn.jquery);
  console.log("Formulario encontrado:", $("#login").length > 0);
});

