<?php
include_once '../config/config.php';
include_once '../control/UsuarioController.php';

$usuarioController = new UsuarioController();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $rol = $_POST['rol'];
    $claveSecreta = $_POST['claveSecreta'] ?? null;

    $resultado = $usuarioController->registrar($nombre, $password, $email, $rol, $claveSecreta);

    if (isset($resultado['error'])) {
        $error = $resultado['error'];
    } else {
        header("Location: login.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1>Registro de Usuario</h1>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    <form action="registrarUsuario.php" method="POST">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Correo Electrónico</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Contraseña</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="mb-3">
            <label for="rol" class="form-label">Rol</label>
            <select class="form-control" id="rol" name="rol" onchange="mostrarClaveSecreta()" required>
                <option value="1">Cliente</option>
                <option value="2">Admin</option>
                <option value="3">Deposito</option>
            </select>
        </div>
        <div class="mb-3" id="claveSecretaDiv" style="display: none;">
            <label for="claveSecreta" class="form-label">Clave Secreta de la Empresa</label>
            <input type="password" class="form-control" id="claveSecreta" name="claveSecreta">
        </div>
        <button type="submit" class="btn btn-primary">Registrarse</button>
    </form>
</div>

<script>
function mostrarClaveSecreta() {
    const rol = document.getElementById("rol").value;
    const claveSecretaDiv = document.getElementById("claveSecretaDiv");
    claveSecretaDiv.style.display = (rol == "2" || rol == "3") ? "block" : "none";
}
</script>
</body>
</html>
