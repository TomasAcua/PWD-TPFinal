<?php
include_once "../../configuracion.php";
session_start();
session_unset();
session_destroy();
header('Location: ../../Vista/index.php?mensaje=' . urlencode('Sesión cerrada exitosamente!'));
exit;
?>