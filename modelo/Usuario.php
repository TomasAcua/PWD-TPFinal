<?php
require_once 'BaseDatos.php';

class Usuario {
    private $conn;
    private $table_name = "usuario";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Obtener usuario por nombre
    public function obtenerPorNombre($nombre_usuario) {
        $query = "SELECT usuario.*, rol.rodescripcion AS rol 
                  FROM usuario 
                  JOIN usuariorol ON usuario.idusuario = usuariorol.idusuario 
                  JOIN rol ON usuariorol.idrol = rol.idrol
                  WHERE usnombre = :nombre_usuario";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":nombre_usuario", $nombre_usuario);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    

    // Registrar nuevo usuario
    public function registrarUsuario($nombre, $password, $mail) {
        $query = "INSERT INTO " . $this->table_name . " (usnombre, uspass, usmail) VALUES (:nombre, :password, :mail)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":password", password_hash($password, PASSWORD_DEFAULT));
        $stmt->bindParam(":mail", $mail);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    public function asignarRol($idUsuario, $idRol) {
        $query = "INSERT INTO usuariorol (idusuario, idrol) VALUES (:idusuario, :idrol)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":idusuario", $idUsuario);
        $stmt->bindParam(":idrol", $idRol);
        return $stmt->execute();
    }
    
}
