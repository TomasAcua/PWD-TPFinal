$(document).ready(function() {
    console.log("Iniciando carga del menú...");
    
    $.ajax({
        url: '/TPFinal/Vista/Estructura/obtenerPermisos.php',
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            console.log("Respuesta del servidor:", response);
            if (response.usuario) {
                console.log("Usuario encontrado:", response.usuario);
                console.log("Permisos:", response.permisos);
                armarMenu(response.permisos);
            }
        },
        error: function(xhr, status, error) {
            console.error("Error al cargar el menú:", error);
            console.error("Status:", xhr.status);
            console.error("Respuesta:", xhr.responseText);
        }
    });
});

function armarMenu(permisos) {
    console.log("Armando menú con permisos:", permisos);
    var menuHtml = '';
    
    // Buscar los menús principales por su nombre
    var menuAdmin = permisos.find(p => p.menombre === 'Administrador Permisos');
    var menuDeposito = permisos.find(p => p.menombre === 'Deposito Permisos');
    var menuCliente = permisos.find(p => p.menombre === 'Tus Compras');
    
    // Menú Administrador
    if (menuAdmin) {
        menuHtml += crearMenuDropdown(menuAdmin);
    }
    
    // Menú Depósito
    if (menuDeposito) {
        menuHtml += crearMenuDropdown(menuDeposito);
    }
    
    // Menú Cliente
    if (menuCliente) {
        menuHtml += crearMenuDropdown(menuCliente);
    }
    
    $('#menu-principal').html(menuHtml);

    // Reinicializar los dropdowns
    setTimeout(function() {
        var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
        dropdownElementList.map(function (dropdownToggleEl) {
            return new bootstrap.Dropdown(dropdownToggleEl);
        });
    }, 100);
}

function crearMenuDropdown(menu) {
    return `
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                ${menu.menombre}
            </a>
            <ul class="dropdown-menu">
                ${menu.hijos.map(hijo => {
                    var ruta = hijo.medescripcion;
                    return `
                        <li>
                            <a class="dropdown-item" href="/TPFinal/Vista/${ruta}">
                                ${hijo.menombre}
                            </a>
                        </li>`;
                }).join('')}
            </ul>
        </li>`;
}

function cerrarSesion() {
    $.ajax({
        url: '/TPFinal/Acciones/login/cerrarSesion.php',
        type: 'POST',
        success: function() {
            window.location.href = '/TPFinal/Vista/index.php';
        }
    });
}

function cambiarRol(nuevoRol) {
    $.ajax({
        url: '/TPFinal/Acciones/login/cambiarRol.php',
        type: 'POST',
        data: { nuevorol: nuevoRol },
        success: function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert("Error al cambiar de rol");
            }
        }
    });
}