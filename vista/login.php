<?php
$Titulo = "Iniciar Sesion";
include_once './Estructura/cabecera.php';
?>

<div class="container p-4 mt-5 border border-secondary border-2 rounded-2 bg-primary bg-opacity-10" style="width: 350px;">
    <h5 class="text-center"><i class="fa-solid fa-person-arrow-up-from-line me-2"></i>Iniciar Sesion</h5>
    <hr>
    <!-- INICIO FORMULARIO INICIAR SESIÓN -->
    <form id="login" action="" method="post" class="mb-3" autocomplete="off" novalidate>
        <div class="form-group mb-3">
            <label for="usnombre" class="form-label">Usuario:</label>
            <input type="text" class="form-control" id="usnombre" name="usnombre" required>
        </div>
        <div class="form-group mb-3">
            <label for="uspass" class="form-label">Contraseña:</label>
            <input type="password" class="form-control" id="uspass" name="uspass" required>
        </div>
        <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
    </form>
    <!-- FIN FORMULARIO INICIAR SESIÓN -->
    <div class="p3">
        No est&aacute; registrado?
        <a href="registro.php">Registrarse</a>
    </div>
</div>

<!-- Primero jQuery -->
<script src="../Utiles/jquery-3.6.1/jquery.min.js"></script>

<!-- Luego Bootstrap -->
<script src="../Utiles/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Bootbox después de Bootstrap -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.5.2/bootbox.min.js"></script>

<!-- MD5 -->
<script src="../Utiles/js/md5.js"></script>

<!-- Tus scripts al final -->
<script src="../Utiles/js/login.js"></script>
<script src="../Utiles/js/validaciones.js"></script>

<!-- CSS -->
<link rel="stylesheet" href="../Utiles/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="../Utiles/validaciones.css">

<?php include_once '.\Estructura\pie.php'; ?>