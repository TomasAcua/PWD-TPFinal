<?php
include_once '../config/config.php';
$usuarioController = new UsuarioController();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = darDatosSubmitted();
    $resultado = $usuarioController->registrar($data['nombre'], $data['password'], $data['email'], $data['rol'], $data['claveSecreta']);
    echo json_encode(['success' => empty($resultado['error']), 'error' => $resultado['error'] ?? null]);
    exit;
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
    <form id="registroForm">
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
        <button type="button" id="registerButton" class="btn btn-primary">Registrarse</button>
    </form>
</div>

<script>
    document.getElementById('registerButton').addEventListener('click', function() {
        const formData = new FormData(document.querySelector('#registroForm'));
        fetch('registrarUsuario.php', { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                if (data.success) window.location.href = 'login.php';
                else alert(data.error);
            });
    });
</script>
</body>
</html>
