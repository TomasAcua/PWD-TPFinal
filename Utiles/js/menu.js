$(document).ready(function() {
    cargarMenu();
    
    // Manejador para cambio de rol
    $('#cambiar-rol').on('change', function() {
        cambiarRol($(this).val());
    });
    
    // Manejador para cerrar sesión
    $('#cerrar-sesion').on('click', function() {
        cerrarSesion();
    });
});

function cargarMenu() {
    $.ajax({
        url: '../Acciones/login/accionMenu.php',
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.usuario) {
                // Actualizar nombre de usuario
                $('#username-display').text(response.usuario.nombre);
                
                // Cargar roles si hay más de uno
                if (response.roles && response.roles.length > 1) {
                    var select = $('#cambiar-rol');
                    select.empty();
                    response.roles.forEach(function(rol) {
                        var selected = (rol.rol === response.usuario.rol) ? 'selected' : '';
                        select.append(`<option value="${rol.rol}" ${selected}>${rol.rol}</option>`);
                    });
                }
                
                // Cargar menú
                var menuHtml = '';
                response.permisos.forEach(function(menu) {
                    if (menu.hijos && menu.hijos.length > 0) {
                        // Menú desplegable
                        menuHtml += `
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    ${menu.menombre}
                                </a>
                                <ul class="dropdown-menu">
                                    ${generarSubMenu(menu.hijos)}
                                </ul>
                            </li>`;
                    } else {
                        // Enlace simple
                        menuHtml += `
                            <li class="nav-item">
                                <a class="nav-link" href="${menu.medescripcion}">${menu.menombre}</a>
                            </li>`;
                    }
                });
                
                $('#menu-principal').html(menuHtml);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar el menú:', error);
        }
    });
}

function generarSubMenu(hijos) {
    var html = '';
    hijos.forEach(function(hijo) {
        if (hijo.hijos && hijo.hijos.length > 0) {
            html += `
                <li class="dropdown-submenu">
                    <a class="dropdown-item dropdown-toggle" href="#">${hijo.menombre}</a>
                    <ul class="dropdown-menu">
                        ${generarSubMenu(hijo.hijos)}
                    </ul>
                </li>`;
        } else {
            html += `<li><a class="dropdown-item" href="${hijo.medescripcion}">${hijo.menombre}</a></li>`;
        }
    });
    return html;
}

function cambiarRol(nuevoRol) {
    $.ajax({
        url: '../Acciones/login/cambiarRol.php',
        type: 'POST',
        data: { nuevorol: nuevoRol },
        success: function(response) {
            if (response.success) {
                location.reload();
            } else {
                bootbox.alert({
                    message: "Error al cambiar de rol",
                    size: 'small'
                });
            }
        }
    });
}

function cerrarSesion() {
    $.ajax({
        url: '../Acciones/login/cerrarSesion.php',
        type: 'POST',
        success: function() {
            window.location.href = 'index.php';
        }
    });
}