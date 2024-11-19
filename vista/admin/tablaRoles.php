<?php
$Titulo = "Tabla Roles";
include_once "../Estructura/cabecera.php";

if (!$sesion->verificarPermiso('../Admin/tablaRoles.php')) {
    $mensaje = "No tiene permiso para acceder a este sitio.";
    echo "<script> window.location.href='../index.php?mensaje=" . urlencode($mensaje) . "'</script>";
} else {
    $objRoles = new abmRol();
    $listaRoles = $objRoles->buscar(null);
?>
    <div class="container my-2">
        <div class="table-responsive">
            <table class="table table-hover caption-top align-middle text-center" id="tablaRoles">
                <thead class="table-dark">
                    <tr>
                        <th width="70">ID</th>
                        <th>Descripción</th>
                        <th width="200">Acciones</th>
                    </tr>
                </thead>
                <tbody class="table-group-divider">
                    <!-- Fila para agregar nuevo rol -->
                    <tr class="table-active" id="filaAgregar">
                        <td><input class="form-control" type="number" placeholder="#" readonly></td>
                        <td><input class="form-control" type="text" id="nuevaDescripcion" placeholder="Nueva descripción"></td>
                        <td>
                            <button class="btn btn-outline-success" onclick="agregarRol()">
                                <i class="fa-solid fa-plus"></i> Agregar
                            </button>
                        </td>
                    </tr>
                    <?php foreach ($listaRoles as $objR) { ?>
                        <tr id="fila-<?php echo $objR->getID() ?>">
                            <td><?php echo $objR->getID() ?></td>
                            <td class="descripcion-rol"><?php echo $objR->getRolDescripcion() ?></td>
                            <td>
                                <button class="btn btn-outline-warning btn-editar" onclick="habilitarEdicion(<?php echo $objR->getID() ?>)">
                                    <i class="fa-solid fa-edit"></i> Editar
                                </button>
                                <button class="btn btn-outline-danger" onclick="eliminarRol(<?php echo $objR->getID() ?>)">
                                    <i class="fa-solid fa-trash"></i> Eliminar
                                </button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
    function agregarRol() {
        const descripcion = document.getElementById('nuevaDescripcion').value;
        if (descripcion.trim() === '') {
            alert('La descripción no puede estar vacía');
            return;
        }

        $.ajax({
            url: '/TPFinal/Acciones/rol/altaRol.php',
            type: 'POST',
            data: { rodescripcion: descripcion },
            dataType: 'json',
            success: function(response) {
                if (response) {
                    alert('Rol agregado correctamente');
                    location.reload();
                } else {
                    alert('Error al agregar el rol');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                console.log('Respuesta:', xhr.responseText);
                alert('Error en la comunicación con el servidor');
            }
        });
    }

    function habilitarEdicion(idRol) {
        const fila = document.getElementById(`fila-${idRol}`);
        const tdDescripcion = fila.querySelector('.descripcion-rol');
        const descripcionActual = tdDescripcion.textContent;
        
        tdDescripcion.innerHTML = `
            <input type="text" class="form-control" value="${descripcionActual}" id="edit-${idRol}">
        `;
        
        const tdBotones = fila.querySelector('td:last-child');
        tdBotones.innerHTML = `
            <button class="btn btn-outline-success" onclick="guardarEdicion(${idRol})">
                <i class="fa-solid fa-save"></i> Guardar
            </button>
            <button class="btn btn-outline-secondary" onclick="cancelarEdicion(${idRol}, '${descripcionActual}')">
                <i class="fa-solid fa-times"></i> Cancelar
            </button>
        `;
    }

    function guardarEdicion(idRol) {
        const nuevaDescripcion = document.getElementById(`edit-${idRol}`).value;
        
        if (nuevaDescripcion.trim() === '') {
            alert('La descripción no puede estar vacía');
            return;
        }

        $.ajax({
            url: '/TPFinal/Acciones/rol/editarRol.php',
            type: 'POST',
            data: {
                idrol: idRol,
                descripcion: nuevaDescripcion
            },
            dataType: 'json',
            success: function(response) {
                if (response) {
                    alert('Rol actualizado correctamente');
                    location.reload();
                } else {
                    alert('Error al actualizar el rol');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                console.log('Respuesta:', xhr.responseText);
                alert('Error en la comunicación con el servidor');
            }
        });
    }

    function cancelarEdicion(idRol, descripcionOriginal) {
        const fila = document.getElementById(`fila-${idRol}`);
        const tdDescripcion = fila.querySelector('.descripcion-rol');
        tdDescripcion.textContent = descripcionOriginal;
        
        const tdBotones = fila.querySelector('td:last-child');
        tdBotones.innerHTML = `
            <button class="btn btn-outline-warning btn-editar" onclick="habilitarEdicion(${idRol})">
                <i class="fa-solid fa-edit"></i> Editar
            </button>
            <button class="btn btn-outline-danger" onclick="eliminarRol(${idRol})">
                <i class="fa-solid fa-trash"></i> Eliminar
            </button>
        `;
    }

    function eliminarRol(idRol) {
        if (confirm('¿Está seguro de que desea eliminar este rol?')) {
            $.ajax({
                url: '/TPFinal/Acciones/rol/eliminarRol.php',
                type: 'POST',
                data: { idrol: idRol },
                dataType: 'json',
                success: function(response) {
                    if (response) {
                        alert('Rol eliminado correctamente');
                        location.reload();
                    } else {
                        alert('Error al eliminar el rol');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    console.log('Respuesta:', xhr.responseText);
                    alert('Error en la comunicación con el servidor');
                }
            });
        }
    }
    </script>

<?php 
}
include_once '../Estructura/pie.php';
?>