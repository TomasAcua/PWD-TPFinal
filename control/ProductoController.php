<?php
include_once '../config/config.php';

class ProductoController {
    private $db;
    private $producto;

    public function __construct() {
        $database = new BaseDatos();
        $this->db = $database->conectar();
        $this->producto = new Producto($this->db);
    }

    public function listarProductos() {
        return $this->producto->obtenerTodos();
    }

    public function agregarProducto($nombre, $detalle, $stock) {
        return $this->producto->agregar($nombre, $detalle, $stock);
    }

    public function eliminarProducto($idproducto) {
        return $this->producto->eliminar($idproducto);
    }
    
    public function actualizarStock($idProducto, $cantidad) {
        return $this->producto->actualizarStock($idProducto, $cantidad);
    }
    public function obtenerProductos() {
        return $this->producto->obtenerTodos();
    }
    
}
