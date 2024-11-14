<?php

$usuarioNombre = $_SESSION['usnombre'] ?? null;
$rol = $_SESSION['rol'] ?? null;
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">Mi Tienda</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php if ($rol): ?>
                    <li class="nav-item">
                        <span class="nav-link">Hola, <?= htmlspecialchars($usuarioNombre); ?> (<?= htmlspecialchars($rol); ?>)</span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="tienda.php">Tienda</a>
                    </li>
                <?php endif; ?>

                <?php if ($rol === 'Cliente'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="carrito.php">Carrito de Compras</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="perfil.php">Mi Cuenta</a>
                    </li>
                <?php endif; ?>

                <?php if ($rol === 'Admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="AdminPanelUsuarios.php">Gestionar Usuarios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="ajustarInventario.php">Ajustar Inventario</a>
                    </li>
                <?php endif; ?>

                <?php if ($rol === 'Deposito'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="ajustarInventario.php">Ajustar Inventario</a>
                    </li>
                <?php endif; ?>

                <?php if ($rol): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Cerrar Sesión</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Iniciar Sesión</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="registrarUsuario.php">Registrarse</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
