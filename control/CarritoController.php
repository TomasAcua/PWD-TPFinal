<?php
include_once '../config/config.php';

class CarritoController {
    private $db;
    private $compra;
    private $compraItem;
    private $producto;
    private $compraEstado;

    public function __construct($db) {
        $this->db = $db;
        $this->compra = new Compra($db);
        $this->compraItem = new CompraItem($db);
        $this->producto = new Producto($db);
        $this->compraEstado = new CompraEstado($db);
    }

    // Crear una nueva compra para el usuario
    public function crearCompra($idUsuario) {
        $idCompra = $this->compra->crearCompra($idUsuario);
        // Asignar el estado inicial "iniciada" (id 1)
        $this->compraEstado->cambiarEstadoCompra($idCompra, 1);
        return $idCompra;
    }

    // Agregar un producto al carrito del usuario
    public function agregarProducto($idCompra, $idProducto, $cantidad) {
        // Verificar si el producto tiene suficiente stock
        $producto = $this->producto->obtenerProductoPorId($idProducto);
        if ($producto['procantstock'] >= $cantidad) {
            // Reducir stock en la tabla de productos
            $nuevoStock = $producto['procantstock'] - $cantidad;
            $this->producto->actualizarStock($idProducto, $nuevoStock);
            
            // Agregar el producto al carrito
            return $this->compraItem->agregarProducto($idCompra, $idProducto, $cantidad);
        } else {
            return ['error' => 'Stock insuficiente para este producto'];
        }
    }

    // Ver el contenido del carrito
    public function verCarrito($idCompra) {
        return $this->compraItem->obtenerProductos($idCompra);
    }

    // Obtener el estado actual de una compra
    public function obtenerEstadoCompra($idCompra) {
        return $this->compraEstado->obtenerEstadoCompra($idCompra);
    }

    // Cambiar el estado de la compra (solo administradores)
    public function cambiarEstadoCompra($idCompra, $nuevoEstado) {
        return $this->compraEstado->cambiarEstadoCompra($idCompra, $nuevoEstado);
    }
}