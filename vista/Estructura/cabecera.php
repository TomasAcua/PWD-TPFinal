<?php
$rootPath = $_SERVER['DOCUMENT_ROOT'] . '/TPFinal/';
require_once $rootPath . 'configuracion.php';
$sesion = new Session();
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $Titulo ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- JQUERY -->
    <script type="text/javascript" src="/TPFinal/Utiles/jquery-3.6.1/jquery.min.js"></script>
    
    <!-- BOOTSTRAP -->
    <link rel="stylesheet" href="/TPFinal/Utiles/bootstrap/css/bootstrap.min.css">
    <script src="/TPFinal/Utiles/bootstrap/js/bootstrap.bundle.min.js"></script>
    
    <!-- FONT AWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- BOOTBOX -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.5.2/bootbox.min.js"></script>
    
    <!-- VALIDATOR -->
    <script src="/TPFinal/Utiles/js/bootstrapValidator.min.js"></script>
    <script src="/TPFinal/Utiles/js/mensajesBVes_ES.js"></script>
    
    <!-- CSS MENÚ -->
    <link rel="stylesheet" href="/TPFinal/Utiles/menu.css">
    
    <!-- ICON -->
    <link rel="icon" type="image/x-icon" href="/TPFinal/Vista/img/icon.ico">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="/TPFinal/Vista/index.php">Brandon Cult</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0" id="menu-principal">
                    <!-- El menú se cargará dinámicamente aquí -->
                </ul>
                
                <?php if ($sesion->activa()): ?>
                    <div class="d-flex align-items-center">
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-2"></i>
                                <span id="username-display"><?php echo $sesion->getNombreUsuarioLogueado(); ?></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="/TPFinal/Vista/Admin/modificarPerfil.php">Mi Perfil</a></li>
                                <?php if (count($sesion->getRoles()) > 1): ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <div class="px-3">
                                            <select class="form-select" id="cambiar-rol">
                                                <?php foreach ($sesion->getRoles() as $rol): ?>
                                                    <option value="<?php echo $rol->getRolDescripcion(); ?>"
                                                            <?php echo ($sesion->getRolActivo()['rol'] === $rol->getRolDescripcion()) ? 'selected' : ''; ?>>
                                                        <?php echo strtoupper($rol->getRolDescripcion()); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="javascript:void(0)" onclick="cerrarSesion()">Cerrar Sesión</a></li>
                            </ul>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="d-flex">
                        <a href="/TPFinal/Vista/login.php" class="btn btn-outline-light me-2">Iniciar Sesión</a>
                        <a href="/TPFinal/Vista/registro.php" class="btn btn-primary">Registrarse</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Scripts -->
    <script src="/TPFinal/Utiles/js/menu.js"></script>
    <!-- Bootstrap Bundle with Popper -->
    <script src="/TPFinal/Utiles/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>