<?php
class Compra {
    private $conn;
    private $table_name = "compra";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Crear una nueva compra
    public function crearCompra($idUsuario) {
        $query = "INSERT INTO " . $this->table_name . " (idusuario) VALUES (:idusuario)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":idusuario", $idUsuario);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }

    // Obtener todas las compras para un usuario
    public function obtenerComprasUsuario($idUsuario) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE idusuario = :idusuario";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":idusuario", $idUsuario);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
