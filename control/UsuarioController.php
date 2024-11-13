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
    public function iniciarSesion($nombre, $password)
    {
        $usuario = $this->usuario->obtenerPorNombre($nombre);
        if ($usuario && password_verify($password, $usuario['uspass'])) {
            $_SESSION['idusuario'] = $usuario['idusuario'];
            $_SESSION['usnombre'] = $usuario['usnombre'];
            $_SESSION['rol'] = $usuario['rol'];
            return true;
        }
        return false;
    }
    public function tieneAcceso($rolesPermitidos)
    {
        return in_array($_SESSION['rol'], $rolesPermitidos);
    }

    // Registro de usuario
    public function registrar($nombre, $password, $mail)
    {
        return $this->usuario->registrarUsuario($nombre, $password, $mail);
    }
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
    public function asignarRol($idUsuario, $idRol)
    {
        // Verifica si el usuario ya tiene un rol
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



    // Cerrar sesión
    public function cerrarSesion()
    {
        session_start();
        session_unset();
        session_destroy();
    }
}
