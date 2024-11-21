<?php

class abmCompra
{

    public function abm($datos)
    {
        $resp = false;
        if ($datos['action'] == 'eliminar') {
            if ($this->baja($datos)) {
                $resp = true;
            }
        }
        if ($datos['action'] == 'modificar') {
            if ($this->modificacion($datos)) {
                $resp = true;
            }
        }
        if ($datos['action'] == 'alta') {
            if ($this->alta($datos)) {
                $resp = true;
            }
        }
        return $resp;
    }

    /**
     * Espera como parametro un arreglo asociativo donde las claves coinciden
     * con los nombres de las variables instancias del objeto
     * @param array $param
     * @return compra
     */
    private function cargarObjeto($param)
    {
        $objUsuario = new usuario();
        $objUsuario->setID($param['objusuario']);
        $objUsuario->cargar();
        $obj = null;
        if (
            array_key_exists('idcompra', $param) &&
            array_key_exists('cofecha', $param) &&
            array_key_exists('objusuario', $param)
        ) {
            $obj = new compra();
            $obj->setear($param['idcompra'], $param['cofecha'], $param['objusuario']);
        }
        return $obj;
    }
    private function cargarObjetoSinID($param)
    {
        $obj = null;
        if (
            array_key_exists('cofecha', $param) &&
            array_key_exists('idusuario', $param)
        ) {
            $objusuario = new usuario();

            $objusuario->setID($param['idusuario']);

            $objusuario->cargar();

            $obj = new compra();
            $obj->setearSinID($param['cofecha'], $objusuario);
        }
        return $obj;
    }

    /**
     * Espera como parametro un arreglo asociativo donde las claves coinciden
     * con los nombres de las variables instancias del objeto que son claves
     * @param array $param
     * @return compra
     */
    private function cargarObjetoConClave($param)
    {
        $obj = null;
        if (isset($param['idcompra'])) {
            $obj = new compra();
            $obj->setear($param['idcompra'], null, null);
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
        if (isset($param['idcompra'])) {
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
        // $param['idrol'] =null;
        $objcompra = $this->cargarObjeto($param);
        // verEstructura($Objrol);
        if ($objcompra != null and $objcompra->insertar()) {
            $resp = true;
        }
        return $resp;
    }

    public function altaSinID($param)
    {
        $resp = false;

        $objCompra = $this->cargarObjetoSinID($param);
        if ($objCompra != null and $objCompra->insertar()) {
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
            $objcompra = $this->cargarObjetoConClave($param);
            if ($objcompra != null and $objcompra->eliminar()) {
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
        // echo "<i>**Realizando la modificación**</i>";

        $resp = false;
        if ($this->seteadosCamposClaves($param)) {
            $objcompra = $this->cargarObjeto($param);
            if ($objcompra != null and $objcompra->modificar()) {
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
        error_log("=== Inicio abmCompra->buscar ===");
        error_log("Parámetros de búsqueda: " . print_r($param, true));
        
        $where = " true ";
        if ($param !== null) {
            if (isset($param['idcompra']))
                $where.=" and idcompra =".$param['idcompra'];
            if (isset($param['cofecha']))
                $where.=" and cofecha ='".$param['cofecha']."'";
            if (isset($param['idusuario']))
                $where.=" and idusuario =".$param['idusuario'];
        }
        
        error_log("WHERE construido: " . $where);
        
        $obj = new compra();
        $arreglo = $obj->listar($where);
        error_log("Resultado de listar: " . print_r($arreglo, true));
        
        return $arreglo;
    }

    /*############### FUNCIONES QUE UTILIZAN LOS ACTION #######################*/

    /* AGREGAR PRODUCTO AL CARRITO */

    public function agregarProdCarrito($datos){
        $resp = false;
        try {
            $session = new Session();
            $idUsuario = $session->getIDUsuarioLogueado();
            
            if (!$idUsuario) {
                throw new Exception("Usuario no logueado");
            }

            $objUsuario = new usuario();
            $objUsuario->setID($idUsuario);
            if (!$objUsuario->cargar()) {
                throw new Exception("Error al cargar usuario");
            }

            // Buscar carrito activo o crear uno nuevo
            $carritoActual = $this->obtenerCarrito($idUsuario);
            if (empty($carritoActual)) {
                $carritoActual = $this->crearCarrito($idUsuario);
            }

            if ($carritoActual) {
                // Agregar producto al carrito
                $objCompraItem = new compraItem();
                $objProducto = new producto();
                $objProducto->setID($datos['idproducto']);
                
                $paramCI = [
                    'idcompraitem' => null,
                    'idproducto' => $datos['idproducto'],
                    'idcompra' => $carritoActual->getID(),
                    'cicantidad' => $datos['cicantidad']
                ];
                
                $abmCI = new abmCompraItem();
                $resp = $abmCI->alta($paramCI);
            }
            
            return $resp;
            
        } catch (Exception $e) {
            error_log("Error en agregarProdCarrito: " . $e->getMessage());
            return false;
        }
    }

    public function sumarProdCarrito($objCompraCarrito, $data)
    {
        //Agrega el producto con cantidad 1 si no existe
        //Si el producto existe, le suma 1 a su cantidad
        $respuesta = false;
        $objAbmCompraItem = new abmCompraItem();
        $idCompra = $objCompraCarrito->getID();
        $param = array(
            'idproducto' => $data['idproducto'],
            'idcompra' => $idCompra
        );
        $listaCompraItem = $objAbmCompraItem->buscar($param);
        if (count($listaCompraItem) > 0) { //si existe el producto ya en el carrito solo lo seteo
            $objCompraItem = $listaCompraItem[0];
            $idCI = $objCompraItem->getID();
            $cantidadCI = $objCompraItem->getCiCantidad();
            $nuevaCantCI = $cantidadCI + 1;
            $paramCI = array(
                'idcompraitem' => $idCI,
                'idproducto' => $data['idproducto'],
                'idcompra' => $idCompra,
                'cicantidad' => $nuevaCantCI
            );
            //print_r($paramCI);
            $respuesta = $objAbmCompraItem->modificacion($paramCI);
            if (!$respuesta) {
                echo "no se modifico";
            }
        } else { //si no lo creo y lo uno con el carrito
            $data['idcompra'] = $idCompra;
            $respuesta = $objAbmCompraItem->altaSinID($data);
        }
        return $respuesta;
    }

    public function crearCarrito($idUsuario)
    {
        try {
            error_log("Iniciando creación de carrito para usuario: " . $idUsuario);
            
            $objUsuario = new usuario();
            $objUsuario->setID($idUsuario);
            if (!$objUsuario->cargar()) {
                throw new Exception("Error al cargar usuario");
            }

            // Crear nueva compra
            $datos = array(
                'idusuario' => $idUsuario,
                'cofecha' => date('Y-m-d H:i:s')
            );

            error_log("Intentando crear compra con datos: " . print_r($datos, true));

            // Usar altaSinID para crear la compra
            if ($this->altaSinID($datos)) {
                // Buscar la compra recién creada
                $compras = $this->buscar($datos);
                if (!empty($compras)) {
                    $compra = $compras[0];
                    
                    // Crear estado inicial (borrador)
                    $abmCompraEstado = new abmCompraEstado();
                    $datosEstado = array(
                        'idcompra' => $compra->getID(),
                        'idcompraestadotipo' => 5, // ID del estado 'borrador'
                        'cefechaini' => date('Y-m-d H:i:s'),
                        'cefechafin' => null
                    );
                    
                    error_log("Intentando crear estado inicial con datos: " . print_r($datosEstado, true));
                    
                    if ($abmCompraEstado->altaSinID($datosEstado)) {
                        error_log("Carrito creado exitosamente con ID: " . $compra->getID());
                        return $compra;
                    }
                }
            }
            
            error_log("No se pudo crear el carrito");
            return null;
            
        } catch (Exception $e) {
            error_log("Error en crearCarrito: " . $e->getMessage());
            return null;
        }
    }

    public function verificarStockProd($objCompraCarrito, $data)
    { //Verifica que la cantidad de stock del producto sea mayor o igual a la nueva cicantidad
        $respuesta = false;
        $objAbmCompraItem = new abmCompraItem();
        $idCompra = $objCompraCarrito->getID();
        $param = array(
            'idproducto' => $data['idproducto'],
            'idcompra' => $idCompra
        );
        $listaCompraItem = $objAbmCompraItem->buscar($param);
        if (count($listaCompraItem) > 0) { //si existe el producto en el carrito chequeo con su cicantidad
            $objCompraItem = $listaCompraItem[0];
            $nuevaCantCI = $objCompraItem->getCiCantidad() + 1;
            $objAbmProd = new abmProducto();
            $param['idproducto'] = $data['idproducto'];
            $listaProd = $objAbmProd->buscar($param);
            if (count($listaProd)) {
                $cantStockProd = $listaProd[0]->getProCantStock();
                if ($cantStockProd >= $nuevaCantCI) {
                    $respuesta = true;
                }
            }
        } else { //si no existe el producto en el carrito no tengo que chequear ningun stock
            $respuesta = true;
        }
        return $respuesta;
    }

    /* FIN METODOS PARA AGREGAR PRODUCTO AL CARRITO */

    /* CANCELAR COMPRA */

    public function cancelarCompra($data)
    {

        $respuesta = false;
        $objCE = new abmCompraEstado();
        $list = $objCE->buscar(['idcompraestado' => $data['idcompraestado']]);

        foreach ($list as $elem) { //RECORREMOS CADA COMPRA ESTADO
            date_default_timezone_set('America/Argentina/Buenos_Aires');
            $idCET = $elem->getObjCompraEstadoTipo()->getID(); //OBTENEMOS EL ID DEL TIPO DE ESTADO
            $fechaIni = $elem->getCeFechaIni(); //FECHA INICIO
            $fechaFin = date('Y-m-d H:i:s'); //FECHA FIN
            $respuesta = $this->cambiarEstado($data, $idCET, $fechaIni, $fechaFin, $objCE);
        }

        return $respuesta;
    }




    public function cambiarEstado($data, $idCET, $fechaIni, $fechaFin, $objCE)
    {

        // PRIMERO ACTUALIZAMOS EL ANTIGUO ESTADO, SETEAMOS SU FECHA FIN
        $arregloModCompra = [
            'idcompraestado' => $data['idcompraestado'],
            'idcompra' => $data['idcompra'],
            'idcompraestadotipo' => $idCET,
            'cefechaini' => $fechaIni,
            'cefechafin' => $fechaFin,
        ];

        // MODIFICAMOS
        $resp = $objCE->modificacion($arregloModCompra);

        if ($resp) { // SI SE PUDO MODIFICAR EL ESTADO ANTERIOR, AGREGAMOS EL NUEVO

            $arregloNewCompra = [
                'idcompra' => $data['idcompra'],
                'idcompraestadotipo' => $data['idcompraestadotipo'],
                'cefechaini' => $fechaFin,
                'cefechafin' => null,
            ];

            $res = $objCE->altaSinID($arregloNewCompra);
        }

        return $res;
    }

    /* FIN CANCELAR COMPRA */

    /* EJECUTAR COMPRA CARRITO */


    public function ejecutarCompraCarrito()
    {
        $objSession = new Session();
        $objAbmUsuario = new abmUsuario();
        $idUserLogueado = $objSession->getIDUsuarioLogueado();
        $carrito = $objAbmUsuario->obtenerCarrito($idUserLogueado);
        return ($this->iniciarCompra($carrito));
    }


    public function iniciarCompra($carrito)
    {
        //modificar fechafin del carrito y crear nueva instancia de compraestado, con idcompraestadotipo =1, unido a la compra.
        date_default_timezone_set('America/Argentina/Buenos_Aires');
        $respuesta = false;
        $objAbmCompraEstado = new abmCompraEstado();
        $idCompra = $carrito->getID();
        $paramCompra = array(
            'idcompra' => $idCompra,
            'idcompraestadotipo' => 1,
            'cefechaini' => date('Y-m-d H:i:s'),
            'cefechafin' => '0000-00-00 00:00:00'
        );

        $respuesta = $objAbmCompraEstado->altaSinID($paramCompra);

        if ($respuesta) {
            $param = array(
                'idcompra' => $idCompra,
                'idcompraestadotipo' => 5,
                'cefechafin' => null
            );
            $listaCompraEstado = $objAbmCompraEstado->buscar($param);
            if (count($listaCompraEstado) > 0) {
                $idCompraEstado = $listaCompraEstado[0]->getID();
                $paramEdicion = array(
                    'idcompraestado' => $idCompraEstado,
                    'idcompra' => $idCompra,
                    'idcompraestadotipo' => 5,
                    'cefechaini' => $listaCompraEstado[0]->getCeFechaIni(),
                    'cefechafin' => date('Y-m-d H:i:s')
                );
                $respuesta = $objAbmCompraEstado->modificacion($paramEdicion);
            }
            // METODO PHPMAILER PARA EL ENVIO DE CORREO
            enviarMail(['idcompra' => $idCompra, 'idcompraestadotipo' => 1]);
        }
        return ['idcompra' => $idCompra, 'respuesta' => $respuesta];
    }

    /* FIN EJECUTAR COMPRA CARRITO */

    /* LISTAR PRODUCTOS CARRITO */

    public function listadoProdCarrito($carrito)
    {
        $arreglo_salida = [];
        if ($carrito <> null) {
            $objCI = new abmCompraItem();
            $arreglo_salida = [];
            $eltosCarrito = $objCI->buscar(['idcompra' => $carrito->getID()]);

            //Recorremos los compraItem del carrito
            foreach ($eltosCarrito as $compraItem) {
                $cant = $compraItem->getCiCantidad();
                $precio = $compraItem->getObjProducto()->getPrecio();
                $nuevoElem = [
                    "idcompraitem" => $compraItem->getID(),
                    "idproducto" => $compraItem->getObjProducto()->getID(),
                    "idcompra" => $compraItem->getObjCompra()->getID(),
                    "imagen" => $compraItem->getObjProducto()->getImagen(),
                    "detalle" => $compraItem->getObjProducto()->getProDetalle(),
                    "pronombre" => $compraItem->getObjProducto()->getProNombre(),
                    "precio" => $precio,
                    "cicantidad" => $cant,
                    "procantstock" => $compraItem->getObjProducto()->getProCantStock(),
                    "subtotal" => ($cant * $precio)
                ];

                array_push($arreglo_salida, $nuevoElem);
            }
        }
        return $arreglo_salida;
    }

    /* FIN LISTAR PRODUCTOS CARRITO */

    /* VACIAR CARRITO */

    public function vaciarCarrito($idCarrito)
    {

        $respuesta = false;
        $objCI = new abmCompraItem();
        $listaCI = $objCI->buscar(['idcompra' => $idCarrito]);
        if (count($listaCI) > 0) {
            foreach ($listaCI as $compraItem) {
                $objCI->baja(['idcompraitem' => $compraItem->getID()]);
            }
            $respuesta = true;
        }
        return $respuesta;
    }

    /* FIN VACIAR CARRITO */

    public function listarComprasUsuarios()
    {
        //Lista todos los datos de compra y compraestado referidos a todos los usuarios
        $arreglo = [];
        $abmUsuario = new abmUsuario();
        $users = $abmUsuario->buscar(null);
        if (count($users) > 0) {
            foreach ($users as $user) {
                $arrDatos = $this->listarCompras($user->getID());
                array_push($arreglo, $arrDatos);
            }
        }

        return $arreglo;
    }

    public function listarCompras($idUsuario = null) {
        try {
            error_log("Ejecutando listarCompras");
            
            $where = " true ";
            if ($idUsuario !== null) {
                $where .= " AND idusuario = " . $idUsuario;
            }
            
            error_log("WHERE clause: " . $where);
            
            $obj = new compra();
            $arreglo = $obj->listar($where);
            error_log("Compras encontradas: " . print_r($arreglo, true));
            
            $resultado = [];
            foreach ($arreglo as $compra) {
                $abmCompraEstado = new abmCompraEstado();
                $estadoActual = $abmCompraEstado->obtenerEstadoActual($compra->getID());
                
                $objUsuario = $compra->getObjUsuario();
                
                $resultado[] = array(
                    'idcompra' => $compra->getID(),
                    'cofecha' => $compra->getCoFecha(),
                    'usnombre' => $objUsuario ? $objUsuario->getUsNombre() : 'N/A',
                    'estado' => $estadoActual['descripcion'],
                    'idcompraestado' => $estadoActual['idcompraestado'],
                    'idcompraestadotipo' => $estadoActual['idcompraestadotipo']
                );
            }
            
            error_log("Resultado final: " . print_r($resultado, true));
            return $resultado;
            
        } catch (Exception $e) {
            error_log("Error en listarCompras: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene el carrito activo del usuario
     * @param int $idUsuario
     * @return compra|null
     */
    public function obtenerCarrito($idUsuario) {
        try {
            // Buscar compra en estado 'borrador' (carrito) para el usuario
            $param = array(
                'idusuario' => $idUsuario
            );
            
            $compras = $this->buscar($param);
            
            if (!empty($compras)) {
                foreach ($compras as $compra) {
                    // Obtener el estado actual de la compra
                    $abmCompraEstado = new abmCompraEstado();
                    $estadoActual = $abmCompraEstado->obtenerEstadoActual($compra->getID());
                    
                    // Si encontramos una compra en estado 'borrador', es el carrito activo
                    if ($estadoActual && $estadoActual['descripcion'] === 'borrador') {
                        return $compra;
                    }
                }
            }
            
            return null;
            
        } catch (Exception $e) {
            error_log("Error en obtenerCarrito: " . $e->getMessage());
            return null;
        }
    }
}