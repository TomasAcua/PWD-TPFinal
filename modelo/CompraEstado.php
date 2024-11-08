<?php
class CompraEstado {
    private $conn;
    private $table_name = "compraestado";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Cambiar el estado de una compra
    public function cambiarEstadoCompra($idCompra, $nuevoEstado) {
        $query = "UPDATE " . $this->table_name . " SET idcompraestadotipo = :nuevoEstado, cefechaini = NOW() WHERE idcompra = :idcompra";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":nuevoEstado", $nuevoEstado);
        $stmt->bindParam(":idcompra", $idCompra);
        return $stmt->execute();
    }

    // Obtener el estado de una compra específica
    public function obtenerEstadoCompra($idCompra) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE idcompra = :idcompra";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":idcompra", $idCompra);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
