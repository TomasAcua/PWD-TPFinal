<?php
session_start(); // Esto debe estar al inicio, antes de cualquier salida de HTML o espacios en blanco
header('Content-Type: text/html; charset=utf-8');
header("Cache-Control: no-cache, must-revalidate");


// Función para encapsular el envío por POST o GET
function darDatosSubmitted() {
    return $_SERVER["REQUEST_METHOD"] === "POST" ? $_POST : $_GET;
}

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



// Autoloader personalizado para cargar las clases del proyecto (modelo, control, vista)
spl_autoload_register(function ($class_name) use ($ROOT) {
    $directories = ["modelo/", "control/", "vista/"];
    foreach ($directories as $directory) {
        $file = $ROOT . $directory . $class_name . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

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

// Guardar la conexión en una variable global para uso en todo el proyecto
$db = conectarBaseDatos();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function enviarCorreo($destinatario, $asunto, $mensaje) {
    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host = $_ENV['SMTP_HOST'];
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['SMTP_USER'];
        $mail->Password = $_ENV['SMTP_PASSWORD'];
        $mail->SMTPSecure = 'tls';
        $mail->Port = $_ENV['SMTP_PORT'];

        // Configuración del remitente
        $mail->setFrom($_ENV['SMTP_FROM'], $_ENV['SMTP_FROM_NAME']);
        $mail->addAddress($destinatario);

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body    = $mensaje;

        $mail->send();
        return true;
    } catch (Exception $e) {
        echo "Error al enviar el mensaje: {$mail->ErrorInfo}";
        return false;
    }
}