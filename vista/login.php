<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="css/estilos.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h2>Iniciar Sesión</h2>
    <form id="loginForm">
        
        <button type="button" id="loginButton" class="btn btn-primary">Ingresar</button>
    </form>
    <label>Usuario:</label>
        <input type="text" name="nombre" required>
        <label>Contraseña:</label>
        <input type="password" name="password" required>
        <button type="submit">Ingresar</button>
    <script>
        document.getElementById('loginButton').addEventListener('click', function() {
            const formData = new FormData(document.querySelector('#loginForm'));
            fetch('accion/verificarLogin.php', { method: 'POST', body: formData })
                .then(response => response.json())
                .then(data => {
                    if (data.success) window.location.href = 'tienda.php';
                    else alert('Error en la autenticación.');
                });
        });
    </script>
</body>
</html>
