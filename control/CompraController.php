<?php
include_once '../config/config.php';

class CompraController {
    private $db;
    private $compra;

    public function __construct() {
        $database = new BaseDatos();
        $this->db = $database->conectar();
        $this->compra = new Compra($this->db);
    }

    public function crearCompra($idUsuario) {
        return $this->compra->crearCompra($idUsuario);
    }

    public function obtenerComprasUsuario($idUsuario) {
        return $this->compra->obtenerComprasUsuario($idUsuario);
    }
    public function obtenerTodasLasCompras() {
        $query = "SELECT compra.idcompra, compra.cofecha, compra.idusuario, 
                         compraestado.idcompraestadotipo AS estado
                  FROM compra
                  JOIN compraestado ON compra.idcompra = compraestado.idcompra";
                  
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function agregarProductoAlCarrito($idCompra, $idProducto, $cantidad) {
        $query = "INSERT INTO compraitem (idcompra, idproducto, cicantidad) VALUES (:idcompra, :idproducto, :cantidad)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":idcompra", $idCompra);
        $stmt->bindParam(":idproducto", $idProducto);
        $stmt->bindParam(":cantidad", $cantidad);
        return $stmt->execute();
    }
    public function obtenerProductosCarrito($idCompra) {
        $query = "SELECT producto.pronombre, producto.prodetalle, compraitem.cicantidad 
                  FROM compraitem 
                  JOIN producto ON compraitem.idproducto = producto.idproducto 
                  WHERE compraitem.idcompra = :idcompra";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":idcompra", $idCompra);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
}
