<?php
class CompraItem {
    private $conn;
    private $table_name = "compraitem";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function agregarProducto($idCompra, $idProducto, $cantidad) {
        $query = "INSERT INTO " . $this->table_name . " (idcompra, idproducto, cicantidad) VALUES (:idcompra, :idproducto, :cantidad)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":idcompra", $idCompra);
        $stmt->bindParam(":idproducto", $idProducto);
        $stmt->bindParam(":cantidad", $cantidad);
        return $stmt->execute();
    }

    public function obtenerProductos($idCompra) {
        $query = "SELECT producto.pronombre, producto.prodetalle, compraitem.cicantidad 
                  FROM " . $this->table_name . " 
                  JOIN producto ON compraitem.idproducto = producto.idproducto 
                  WHERE compraitem.idcompra = :idcompra";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":idcompra", $idCompra);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
