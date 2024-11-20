<?php
$Titulo = "Tabla Usuarios";
include_once "../Estructura/cabecera.php";

// Logs temporales para debug
error_log("=== DEBUG tablaUsuarios.php ===");
$rolActivo = $sesion->getRolActivo();
error_log("Rol activo: " . print_r($rolActivo, true));

$objMR = new abmMenuRol();
$listaMR = $objMR->buscar(['idrol' => $rolActivo['id']]);
error_log("Menús del rol: " . print_r($listaMR, true));

if (!$sesion->verificarPermiso('admin/tablaUsuarios.php')) {
    error_log("Permiso denegado para admin/tablaUsuarios.php");
    $mensaje = "No tiene permiso para acceder a este sitio.";
    echo "<script> window.location.href='/TPFinal/Vista/index.php?mensaje=" . urlencode($mensaje) . "'</script>";
    exit;
}
error_log("Permiso concedido para admin/tablaUsuarios.php");

$objUsuarios = new abmUsuario();
$listaUsuario = $objUsuarios->buscar(null);
?>
    <div class="container my-2">
        <div class="table-responsive">
            <table class="table table-hover caption-top align-middle text-center" id="tablaUsuarios">
                <thead class="table-dark">
                    <tr>
                        <th width="70">ID</th>
                        <th>Nombre</th>
                        <th>Mail</th>
                        <th>Deshabilitado</th>
                        <th width="125">Roles</th>
                        <th width="425">Acciones</th>
                    </tr>
                </thead>
                <tbody class="table-group-divider">
                    <?php foreach ($listaUsuario as $usuario) { ?>
                        <tr>
                            <td><?php echo $usuario->getID() ?></td>
                            <td><?php echo $usuario->getUsnombre() ?></td>
                            <td><?php echo $usuario->getUsmail() ?></td>
                            <td><?php echo $usuario->getUsdeshabilitado() ? $usuario->getUsdeshabilitado() : 'No' ?></td>
                            <td>
                                <?php 
                                $roles = $objUsuarios->traerRoles(['idusuario' => $usuario->getID()]);
                                echo implode(', ', array_map(function($rol) { 
                                    return $rol['text']; 
                                }, $roles));
                                ?>
                            </td>
                            <td>
                                <button type="button" class="btn btn-outline-danger eliminarRol me-2" data-id="<?php echo $usuario->getID() ?>">
                                    <i class="fa-solid fa-book-skull me-2"></i>Quitar Rol
                                </button>
                                <button type="button" class="btn btn-outline-warning agregarRol me-2" data-id="<?php echo $usuario->getID() ?>">
                                    <i class="fa-solid fa-book-medical me-2"></i>Agregar Rol
                                </button>
                                <?php if (!$usuario->getUsdeshabilitado()) { ?>
                                    <button type="button" class="btn btn-outline-secondary deshabilitar" data-id="<?php echo $usuario->getID() ?>">
                                        <i class="fa-solid fa-ban me-2"></i>Deshabilitar
                                    </button>
                                <?php } else { ?>
                                    <button type="button" class="btn btn-outline-success habilitar" data-id="<?php echo $usuario->getID() ?>">
                                        <i class="fa-solid fa-square-check me-2"></i>Habilitar
                                    </button>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="/TPFinal/Utiles/js/funcionesABMUsuario.js"></script>

<?php 
include_once '../Estructura/pie.php';
?>