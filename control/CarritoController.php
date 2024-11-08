<?php
require_once '../modelo/BaseDatos.php';
require_once '../modelo/CompraItem.php';

class CarritoController {
    private $db;
    private $compraItem;

    public function __construct() {
        $database = new BaseDatos();
        $this->db = $database->conectar();
        $this->compraItem = new CompraItem($this->db);
    }

    public function agregarProducto($idCompra, $idProducto, $cantidad) {
        return $this->compraItem->agregarProducto($idCompra, $idProducto, $cantidad);
    }

    public function verCarrito($idCompra) {
        return $this->compraItem->obtenerProductos($idCompra);
    }
}
