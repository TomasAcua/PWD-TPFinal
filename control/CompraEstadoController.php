<?php
require_once '../modelo/BaseDatos.php';
require_once '../modelo/CompraEstado.php';

class CompraEstadoController {
    private $db;
    private $compraEstado;

    public function __construct() {
        $database = new BaseDatos();
        $this->db = $database->conectar();
        $this->compraEstado = new CompraEstado($this->db);
    }

    public function cambiarEstadoCompra($idCompra, $nuevoEstado) {
        return $this->compraEstado->cambiarEstadoCompra($idCompra, $nuevoEstado);
    }

    public function obtenerEstadoCompra($idCompra) {
        return $this->compraEstado->obtenerEstadoCompra($idCompra);
    }
}
