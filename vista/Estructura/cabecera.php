<?php
include_once '../configuracion.php';
$sesion = new Session();
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $Titulo ?></title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- JQUERY -->
    <script type="text/javascript" src="..\Utiles\jquery-3.6.1\jquery.min.js"></script>
    
    <!-- BOOTSTRAP -->
    <link rel="stylesheet" href="..\Utiles\bootstrap\css\bootstrap.min.css">
    
    <!-- CSS MENÚ -->
    <link rel="stylesheet" href="..\Utiles\menu.css">
    <!-- ICON -->
    <link rel="icon" type="image/x-icon" href="./img/icon.ico">
    
    <?php include_once('../configuracion.php'); ?>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Brandon Cult</a>
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
                                <span id="username-display"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="../Vista/modificarPerfil.php">Mi Perfil</a></li>
                                <?php if (count($sesion->getRoles()) > 1): ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <div class="px-3">
                                            <select class="form-select" id="cambiar-rol">
                                                <!-- Los roles se cargarán dinámicamente -->
                                            </select>
                                        </div>
                                    </li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="javascript:void(0)" id="cerrar-sesion">Cerrar Sesión</a></li>
                            </ul>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="d-flex">
                        <a href="login.php" class="btn btn-outline-light me-2">Iniciar Sesión</a>
                        <a href="registro.php" class="btn btn-primary">Registrarse</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <script src="../Utiles/js/menu.js"></script>
</body>
</html>