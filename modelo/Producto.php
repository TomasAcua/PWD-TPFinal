<?php
require_once 'BaseDatos.php';

class Producto {
    private $conn;
    private $table_name = "producto";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function obtenerTodos() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function agregar($nombre, $detalle, $stock) {
        $query = "INSERT INTO " . $this->table_name . " (pronombre, prodetalle, procantstock) VALUES (:nombre, :detalle, :stock)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":detalle", $detalle);
        $stmt->bindParam(":stock", $stock);
        return $stmt->execute();
    }

    public function eliminar($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE idproducto = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
    public function actualizarStock($id, $cantidad) {
        $query = "UPDATE " . $this->table_name . " SET procantstock = :cantidad WHERE idproducto = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":cantidad", $cantidad);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
    public function obtenerProductoPorId($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE idproducto = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    
}
