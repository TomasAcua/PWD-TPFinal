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
    public function modificarEstado($datos)
    {
        $resp = false;
        $list = $this->buscar(['idcompraestado' => $datos['idcompraestado']]);
        foreach ($list as $elem) { //RECORREMOS CADA COMPRA ESTADO

            if ($datos['idcompraestadotipo'] == 2) { // SI EL ESTADOTIPO ES ACEPTADA, HAY QUE VERIFICAR SI SE PUEDE CAMBIAR EL STOCK
                $objCI = new abmCompraItem();

                if ($this->verificarStock($datos['idcompra'])) { // SI HAY MODIFICAMOS LA CANTIDAD DE LOS PRODUCTOS Y FINALMENTE SETEAMOS EL NUEVO COMPRAESTADO
                    $objCI->modificarCantidad($datos['idcompra']);
                    $resp = $this->cambiarEstado($datos, $elem);
                }
            } else if(($datos['idcompraestadotipo'] == 4) && ($elem->getObjCompraEstadoTipo()->getID() == 2)){ //SI QUIERER CANCELAR UNA COMPRA YA ACEPTADA
                if ($this->devolverStock($datos['idcompra'])){
                    $resp = $this->cambiarEstado($datos, $elem);
                }
            } else { // SI NO SIMPLEMENTE CAMBIAMOS DE ESTADO
                $resp = $this->cambiarEstado($datos, $elem);
            }
        }
        // FUNCION PHPMAILER PARA EL ENVIO DEL CORREO POR EL CAMBIO DE ESTADO
        enviarMail($datos);

        return $resp;
    }

    /**
     * verifica si se puede modificar el stock de todos los objetos relacionados a esa compraItem
     * @param array $param
     * @return boolean
     */
    public function verificarStock($idcompra)
    {
        $objCI = new abmCompraItem();
        $list = $objCI->buscar(['idcompra' => intval($idcompra)]); // ARREGLO DE OBJETOS COMPRAITEM
        $verficador = true; // INDICARÁ SI SE PUDIERON MODIFICAR EL STOCK DE TODOS LOS PRODUCTOS
        foreach ($list as $CIactual) {
            if (!($CIactual->getObjProducto()->getProCantStock() >= $CIactual->getCiCantidad())) {
                $verficador = false; // SI LA CANTIDAD DE LA COMPRA ES MAYOR AL STOCK NEGAMOS
            }
        }

        return $verficador;
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
            
            // Obtener el ID del nuevo tipo de estado
            $abmCompraEstadoTipo = new abmCompraEstadoTipo();
            $tiposEstado = $abmCompraEstadoTipo->buscar(['cetdescripcion' => $nuevoEstado]);
            
            if (empty($tiposEstado)) {
                throw new Exception("Tipo de estado no válido");
            }
            
            // Si hay un estado actual, cerrarlo
            if ($estadoActual['idcompraestado']) {
                $this->finalizarEstado($estadoActual['idcompraestado']);
            }
            
            // Crear nuevo estado
            $datos = [
                'idcompra' => $idcompra,
                'idcompraestadotipo' => $tiposEstado[0]->getID(),
                'cefechaini' => date('Y-m-d H:i:s'),
                'cefechafin' => null,
                'action' => 'alta'
            ];
            
            if (!$this->alta($datos)) {
                throw new Exception("No se pudo crear el nuevo estado");
            }
            
            // Si el estado es "aceptada", actualizar stock
            if ($nuevoEstado === 'aceptada') {
                $abmCompraItem = new abmCompraItem();
                $abmCompraItem->modificarCantidad($idcompra);
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
        error_log("Obteniendo estado actual para compra ID: " . $idcompra);
        
        try {
            $obj = new compraEstado();
            // Modificamos el WHERE para obtener el último estado activo
            $where = " idcompra = " . $idcompra . " AND cefechafin IS NULL";
            error_log("Ejecutando búsqueda con WHERE: " . $where);
            
            $estados = $obj->listar($where);
            error_log("Estados encontrados: " . print_r($estados, true));
            
            if (count($estados) > 0) {
                $estadoActual = $estados[0];
                $objCompraEstadoTipo = $estadoActual->getObjCompraEstadoTipo();
                
                return array(
                    'idcompraestado' => $estadoActual->getID(),
                    'descripcion' => $objCompraEstadoTipo->getCetDescripcion(),
                    'idcompraestadotipo' => $objCompraEstadoTipo->getID()
                );
            }
            
            return array(
                'idcompraestado' => null,
                'descripcion' => 'sin estado',
                'idcompraestadotipo' => null
            );
            
        } catch (Exception $e) {
            error_log("Error en obtenerEstadoActual: " . $e->getMessage());
            return array(
                'idcompraestado' => null,
                'descripcion' => 'error',
                'idcompraestadotipo' => null
            );
        }
    }
}