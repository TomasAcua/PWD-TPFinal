<?php
header('Content-Type: text/html; charset=utf-8');
header("Cache-Control: no-cache, must-revalidate");

// Autoload de Composer 
require_once __DIR__ . '/../vendor/autoload.php';

$PROYECTO = "TPFINAL";

// Cargar las variables de entorno desde .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Configuración de la base de datos
$DB_HOST = $_ENV['DB_HOST'];
$DB_NAME = $_ENV['DB_NAME'];
$DB_USER = $_ENV['DB_USER'];
$DB_PASS = $_ENV['DB_PASS'];
$DB_PORT = $_ENV['DB_PORT'];
$DB_ENGINE = $_ENV['DB_ENGINE'];

// Variable de ruta raíz del proyecto
$ROOT = $_SERVER['DOCUMENT_ROOT'] . "/$PROYECTO/";
$_SESSION['ROOT'] = $ROOT;

// Incluir funciones comunes
include_once($ROOT . 'util/funciones.php');

// Configuración de conexión a la base de datos
function conectarBaseDatos() {
    global $DB_HOST, $DB_NAME, $DB_USER, $DB_PASS, $DB_PORT, $DB_ENGINE;
    try {
        $dsn = "$DB_ENGINE:host=$DB_HOST;dbname=$DB_NAME;port=$DB_PORT;charset=utf8";
        $conexion = new PDO($dsn, $DB_USER, $DB_PASS);
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conexion;
    } catch (PDOException $e) {
        echo "Error de conexión: " . $e->getMessage();
        exit();
    }
}
