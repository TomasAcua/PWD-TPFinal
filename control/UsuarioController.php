<?php
include_once '../config/config.php';

class UsuarioController
{
    private $db;
    private $usuario;

    public function __construct()
    {
        $database = new BaseDatos();
        $this->db = $database->conectar();
        $this->usuario = new Usuario($this->db);
    }

    // Iniciar sesión
    public function iniciarSesion($nombre, $password) {
        $usuario = $this->usuario->obtenerPorNombre($nombre);
        if (!$usuario) {
            echo "Usuario no encontrado"; // Mensaje de depuración
            return false;
        }
        if (password_verify($password, $usuario['uspass'])) {
            $_SESSION['idusuario'] = $usuario['idusuario'];
            $_SESSION['usnombre'] = $usuario['usnombre'];
            $_SESSION['rol'] = $usuario['rol'];
            return true;
        }
        echo "Contraseña incorrecta"; // Mensaje de depuración
        return false;
    }    

    public function tieneAcceso($rolesPermitidos)
    {
        return in_array($_SESSION['rol'], $rolesPermitidos);
    }

    // Registro de usuario con validación de clave secreta
    public function registrar($nombre, $password, $mail, $rol, $claveSecreta = null)
    {
        // Validar clave secreta para roles especiales
        if (($rol === 'admin' && $claveSecreta !== '444') || ($rol === 'deposito' && $claveSecreta !== '333')) {
            return ['error' => 'Clave secreta incorrecta para el rol seleccionado.'];
        }

        // Registro del usuario en la base de datos
        if ($this->usuario->registrarUsuario($nombre, $password, $mail)) {
            $idUsuario = $this->db->lastInsertId();
            return $this->asignarRol($idUsuario, $rol);
        }

        return ['error' => 'Error al registrar el usuario.'];
    }

    // Obtener todos los usuarios
    public function obtenerUsuarios()
    {
        $query = "SELECT usuario.idusuario, usuario.usnombre, rol.rodescripcion AS rol 
                  FROM usuario 
                  LEFT JOIN usuariorol ON usuario.idusuario = usuariorol.idusuario 
                  LEFT JOIN rol ON usuariorol.idrol = rol.idrol";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Asignar rol a un usuario
    public function asignarRol($idUsuario, $idRol)
    {
        // Verificar si el usuario ya tiene un rol
        $query = "SELECT * FROM usuariorol WHERE idusuario = :idusuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":idusuario", $idUsuario);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Actualizar rol existente
            $query = "UPDATE usuariorol SET idrol = :idrol WHERE idusuario = :idusuario";
        } else {
            // Insertar nuevo rol
            $query = "INSERT INTO usuariorol (idusuario, idrol) VALUES (:idusuario, :idrol)";
        }

        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":idusuario", $idUsuario);
        $stmt->bindParam(":idrol", $idRol);
        return $stmt->execute();
    }
    public function obtenerIdUsuario($nombre) {
        $query = "SELECT idusuario FROM usuario WHERE usnombre = :nombre";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":nombre", $nombre);
        $stmt->execute();
        return $stmt->fetchColumn(); // Retorna el id del usuario
    }
    
    public function obtenerRol($nombre) {
        $query = "SELECT rol.rodescripcion FROM usuario 
                  INNER JOIN usuariorol ON usuario.idusuario = usuariorol.idusuario
                  INNER JOIN rol ON usuariorol.idrol = rol.idrol
                  WHERE usuario.usnombre = :nombre";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":nombre", $nombre);
        $stmt->execute();
        return $stmt->fetchColumn(); // Retorna la descripción del rol
    }

    // Cerrar sesión
    public function cerrarSesion()
    {
        session_start();
        session_unset();
        session_destroy();
    }
}
