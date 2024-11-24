<?php
require_once __DIR__ . '/../Utiles/funcionesMailer.php';

class abmCompraEstado
{
    /**
     * Espera como parametro un arreglo asociativo donde las claves coinciden
     * con los nombres de las variables instancias del objeto
     * @param array $param
     * @return compraEstado
     */
    private function cargarObjeto($param)
    {
        $obj = null;
        if (
            array_key_exists('idcompraestado', $param) &&
            array_key_exists('idcompra', $param) &&
            array_key_exists('idcompraestadotipo', $param) &&
            array_key_exists('cefechaini', $param) &&
            array_key_exists('cefechafin', $param)
        ) {
            $objcompraestadotipo = new compraEstadoTipo();
            $objcompra = new compra();

            $objcompra->setID($param['idcompra']);
            $objcompraestadotipo->setID($param['idcompraestadotipo']);

            $objcompra->cargar();
            $objcompraestadotipo->cargar();

            $obj = new compraEstado();
            $obj->setear($param['idcompraestado'], $objcompra, $objcompraestadotipo, $param['cefechaini'], $param['cefechafin']);
        }
        return $obj;
    }

    private function cargarObjetoSinID($param)
    {
        $obj = null;
        if (
            array_key_exists('idcompra', $param) &&
            array_key_exists('idcompraestadotipo', $param) &&
            array_key_exists('cefechaini', $param) &&
            array_key_exists('cefechafin', $param)
        ) {
            $objcompraestadotipo = new compraEstadoTipo();
            $objcompra = new compra();

            $objcompra->setID($param['idcompra']);
            $objcompraestadotipo->setID($param['idcompraestadotipo']);

            $objcompra->cargar();
            $objcompraestadotipo->cargar();

            $obj = new compraEstado();
            $obj->setearSinID($objcompra, $objcompraestadotipo, $param['cefechaini'], $param['cefechafin']);
        }
        return $obj;
    }

    /**
     * Espera como parametro un arreglo asociativo donde las claves coinciden
     * con los nombres de las variables instancias del objeto que son claves
     * @param array $param
     * @return compraEstado
     */
    private function cargarObjetoConClave($param)
    {
        $obj = null;
        if (isset($param['idcompraestado'])) {
            $obj = new compraEstado();
            $obj->setear($param['idcompraestado'], null, null, null, null);
        }
        return $obj;
    }

    /**
     * Corrobora que dentro del arreglo asociativo estan seteados los campos claves
     * @param array $param
     * @return boolean
     */
    private function seteadosCamposClaves($param)
    {
        $resp = false;
        if (isset($param['idcompraestado'])) {
            $resp = true;
        }
        return $resp;
    }

    public function altaSinID($param)
    {
        $resp = false;

        $objCE = $this->cargarObjetoSinID($param);
        if ($objCE != null and $objCE->insertar()) {
            $resp = true;
        }
        return $resp;
    }

    /**
     *
     * @param array $param
     */
    public function alta($param)
    {
        $resp = false;
        $objcompraestado = $this->cargarObjeto($param);
        if ($objcompraestado != null and $objcompraestado->insertar()) {
            $resp = true;
        }
        return $resp;
    }

    /**
     * permite eliminar un objeto
     * @param array $param
     * @return boolean
     */
    public function baja($param)
    {
        $resp = false;
        if ($this->seteadosCamposClaves($param)) {
            $objcompraestado = $this->cargarObjetoConClave($param);
            if ($objcompraestado != null and $objcompraestado->eliminar()) {
                $resp = true;
            }
        }

        return $resp;
    }

    /**
     * permite modificar un objeto
     * @param array $param
     * @return boolean
     */
    public function modificacion($param)
    {
        $resp = false;
        if ($this->seteadosCamposClaves($param)) {
            $objcompraestado = $this->cargarObjeto($param);
            if ($objcompraestado != null and $objcompraestado->modificar()) {
                $resp = true;
            }
        }
        return $resp;
    }

    /**
     * permite buscar un objeto
     * @param array $param
     * @return array
     */
    public function buscar($param)
    {
        $where = " true ";
        if ($param <> null) {
            if (isset($param['idcompraestado'])) {
                $where .= " and idcompraestado ='" . $param['idcompraestado'] . "'";
            }
            if (isset($param['idcompra'])) {
                $where .= " and idcompra ='" . $param['idcompra'] . "'";
            }
            if (isset($param['idcompraestadotipo'])) {
                $where .= " and idcompraestadotipo ='" . $param['idcompraestadotipo'] . "'";
            }
            if (isset($param['cefechaini'])) {
                $where .= " and cefechaini ='" . $param['cefechaini'] . "'";
            }
            if (isset($param['cefechafin'])) {
                $where .= " and cefechafin ='" . $param['cefechafin'] . "'";
            }
        }
        $objCE = new compraEstado();
        $arreglo = $objCE->listar($where);
        return $arreglo;
    }

    /**
     * lista las comprasestado menos los carritos
     * @param array $param
     * @return array
     */

    public function listarCompras($datos)
    {
        $arreglo = [];
        $listaCE = $this->buscar($datos);
        if (count($listaCE) > 0) {
            //RECORREMOS EL LISTADO DE COMPRAS ESTADO
            foreach ($listaCE as $compraEstadoActual) {
                // SI NO ES UN CARRITO LO SUMAMOS AL ARREGLO
                if (!($compraEstadoActual->getObjCompraEstadoTipo()->getCetDescripcion() === "carrito")) {
                    $nuevoElem = [
                        "idcompra" => $compraEstadoActual->getObjCompra()->getID(),
                        "cofecha" => $compraEstadoActual->getCeFechaIni(),
                        "finfecha" => $compraEstadoActual->getCeFechaFin(),
                        "usnombre" => $compraEstadoActual->getObjCompra()->getObjUsuario()->getUsNombre(),
                        "estado" => $compraEstadoActual->getObjCompraEstadoTipo()->getCetDescripcion(),
                        "idcompraestado" => $compraEstadoActual->getID()
                    ];
                    array_push($arreglo, $nuevoElem);
                }
            }
        }

        return $arreglo;
    }

    /**
     * modifica el estado del compraestado, si es aceptada o cancelada (despues de aceptada) verifica si hay stock suficiente
     * o devuelve según sea el caso
     * @param array $param
     * @return boolean
     */
    public function modificarEstado($datos) {
        try {
            // Obtener el estado actual
            $estadoActual = $this->obtenerEstadoActual($datos['idcompra']);
            if (!$estadoActual) {
                throw new Exception("No se encontró el estado actual");
            }

            // Si vamos a enviar la compra (estado 3)
            if ($datos['idcompraestadotipo'] == 3) {
                $objCI = new abmCompraItem();
                // Primero verificamos el stock
                if (!$this->verificarStock($datos['idcompra'])) {
                    throw new Exception("No hay suficiente stock para algunos productos");
                }
                
                // Si hay stock, modificar cantidades
                try {
                    $objCI->modificarCantidad($datos['idcompra']);
                } catch (Exception $e) {
                    throw new Exception("Error al modificar stock: " . $e->getMessage());
                }
            }

            // Preparar datos para cerrar el estado actual
            $datosModificacion = array(
                'idcompraestado' => $estadoActual['idcompraestado'],
                'idcompra' => $estadoActual['idcompra'],
                'idcompraestadotipo' => $estadoActual['idcompraestadotipo'],
                'cefechaini' => $estadoActual['cefechaini'],
                'cefechafin' => date('Y-m-d H:i:s')
            );

            // Cerrar el estado actual
            if (!$this->modificacion($datosModificacion)) {
                throw new Exception("Error al cerrar el estado actual");
            }

            // Crear nuevo estado
            $nuevoEstado = array(
                'idcompraestado' => null, // Asegurarnos que sea null para que autoincrement funcione
                'idcompra' => $datos['idcompra'],
                'idcompraestadotipo' => $datos['idcompraestadotipo'],
                'cefechaini' => date('Y-m-d H:i:s'),
                'cefechafin' => null
            );
            
            if (!$this->alta($nuevoEstado)) {
                throw new Exception("Error al crear el nuevo estado");
            }

            return true;

        } catch (Exception $e) {
            error_log("Error en modificarEstado: " . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    /**
     * verifica si se puede modificar el stock de todos los objetos relacionados a esa compraItem
     * @param array $param
     * @return boolean
     */
    private function verificarStock($idcompra) {
        $resp = false;
        try {
            // Obtener los items de la compra
            $objCompraItem = new abmCompraItem();
            $param = array('idcompra' => $idcompra);
            $listaItems = $objCompraItem->buscar($param);
            
            $stockSuficiente = true;
            foreach ($listaItems as $item) {
                // Crear objeto producto y cargarlo
                $objProducto = new producto();
                $objProducto->setID($item->getObjProducto());  // Asumiendo que getObjProducto() devuelve el ID
                if ($objProducto->cargar()) {
                    // Verificar si hay suficiente stock
                    if ($objProducto->getProCantStock() < $item->getCicantidad()) {
                        $stockSuficiente = false;
                        break;
                    }
                } else {
                    $stockSuficiente = false;
                    break;
                }
            }
            
            $resp = $stockSuficiente;
            
        } catch (Exception $e) {
            error_log("Error en verificarStock: " . $e->getMessage());
            $resp = false;
        }
        return $resp;
    }

    /**
     * setea la fecha fin del antiguo estado, y crea el siguiente
     * @param array $param
     * @return boolean
     */
    public function cambiarEstado($idcompra, $nuevoEstado) {
        try {
            // Obtener el estado actual
            $estadoActual = $this->obtenerEstadoActual($idcompra);
            
            if (!$estadoActual) {
                throw new Exception("No se encontró el estado actual de la compra");
            }
            
            // Primero, cerrar el estado actual estableciendo la fecha fin
            $datosModificacion = array(
                'idcompraestado' => $estadoActual['idcompraestado'],
                'idcompra' => $estadoActual['idcompra'],
                'idcompraestadotipo' => $estadoActual['idcompraestadotipo'],
                'cefechaini' => $estadoActual['cefechaini'],
                'cefechafin' => date('Y-m-d H:i:s')
            );
            
            if (!$this->modificacion($datosModificacion)) {
                throw new Exception("Error al cerrar el estado actual");
            }
            
            // Crear el nuevo estado
            $datosNuevoEstado = array(
                'idcompra' => $idcompra,
                'idcompraestadotipo' => $nuevoEstado, // Estado "aceptada"
                'cefechaini' => date('Y-m-d H:i:s'),
                'cefechafin' => null
            );
            
            if (!$this->altaSinID($datosNuevoEstado)) {
                throw new Exception("Error al crear el nuevo estado");
            }
            
            return true;
            
        } catch (Exception $e) {
            error_log("Error en cambiarEstado: " . $e->getMessage());
            throw $e;
        }
    }

    private function finalizarEstado($idcompraestado) {
        $datos = [
            'idcompraestado' => $idcompraestado,
            'cefechafin' => date('Y-m-d H:i:s'),
            'action' => 'modificar'
        ];
        return $this->modificacion($datos);
    }

    /**
    * devuelve el stock de esa compra
    * @param array $param
    * @return boolean
    */
    public function devolverStock($idcompra){
        $resp = true;
        $objCI = new abmCompraItem();
        $list = $objCI->buscar(['idcompra' => intval($idcompra)]);
        foreach($list as $CIactual){
            $stockProducto = $CIactual->getObjProducto()->getProCantStock(); // STOCK ACTUAL
            $cantidad = $CIactual->getCiCantidad(); // CANTIDAD A DEVOLVER

            $CIactual->getObjProducto()->setProCantStock($stockProducto + $cantidad); // SETEAMOS EL NUEVO STOCK

            if (!$CIactual->getObjProducto()->modificar()){ // MODIFICAMOS
                $resp = false;
            }
        }

        return $resp;
    }

    public function obtenerEstadoActual($idcompra) {
        try {
            $where = " idcompra = {$idcompra}";
            $estados = $this->buscar(['idcompra' => $idcompra]);
            
            $estadoActual = null;
            $fechaIniMasReciente = null;
            
            foreach ($estados as $estado) {
                // Solo considerar estados sin fecha fin
                if ($estado->getCeFechaFin() === null) {
                    $fechaIni = strtotime($estado->getCeFechaIni());
                    
                    // Actualizar si es el primer estado o si es más reciente
                    if ($estadoActual === null || $fechaIni > $fechaIniMasReciente) {
                        $estadoActual = $estado;
                        $fechaIniMasReciente = $fechaIni;
                    }
                }
            }
            
            if ($estadoActual) {
                return array(
                    'idcompraestado' => $estadoActual->getID(),
                    'idcompra' => $estadoActual->getObjCompra()->getID(),
                    'idcompraestadotipo' => $estadoActual->getObjCompraEstadoTipo()->getID(),
                    'cefechaini' => $estadoActual->getCeFechaIni(),
                    'cefechafin' => $estadoActual->getCeFechaFin()
                );
            }
            
            return null;
        } catch (Exception $e) {
            error_log("Error en obtenerEstadoActual: " . $e->getMessage());
            throw new Exception("Error al obtener estado actual: " . $e->getMessage());
        }
    }
}