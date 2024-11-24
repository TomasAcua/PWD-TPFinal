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
        $where = " true ";
        if ($param) {
            if (isset($param['idusuario'])) {
                $where .= " AND idusuario = " . $param['idusuario'];
            }
            
            if (isset($param['estado']) && $param['estado'] == 'iniciada') {
                // Modificamos la subconsulta para verificar que no tenga fecha fin
                $where .= " AND EXISTS (
                    SELECT 1 
                    FROM compraestado ce 
                    WHERE ce.idcompra = compra.idcompra 
                    AND ce.idcompraestadotipo = 1 
                    AND ce.cefechafin IS NULL
                )";
            }
        }
        
        $obj = new compra();
        return $obj->listar($where);
    }

    /*############### FUNCIONES QUE UTILIZAN LOS ACTION #######################*/

    /* AGREGAR PRODUCTO AL CARRITO */

    public function agregarProdCarrito($datos) {
        try {
            // Validar datos de entrada
            if (!isset($datos['idproducto']) || !isset($datos['cicantidad'])) {
                throw new Exception("Datos incompletos");
            }

            // Cargar el producto
            $objProducto = new producto();
            $objProducto->setIdproducto($datos['idproducto']);
            if (!$objProducto->cargar()) {
                throw new Exception("Error al cargar el producto");
            }

            // Verificar stock
            if ($objProducto->getProcantstock() < $datos['cicantidad']) {
                throw new Exception("Stock insuficiente");
            }

            // Obtener usuario actual
            $sesion = new Session();
            $objUsuario = $sesion->getUsuario();
            
            if (!$objUsuario) {
                throw new Exception("Usuario no encontrado en la sesión");
            }

            // Buscar compra iniciada
            $compraActiva = $this->buscarCompraIniciada($objUsuario->getID());
            
            if (!$compraActiva) {
                // Crear nueva compra
                $nuevaCompra = new compra();
                $nuevaCompra->setObjUsuario($objUsuario);
                $nuevaCompra->setCoFecha(date('Y-m-d H:i:s'));
                
                if (!$nuevaCompra->insertar()) {
                    throw new Exception("Error al crear nueva compra");
                }
                
                // Crear estado inicial (id 1 = estado inicial/borrador)
                $objCompraEstado = new compraEstado();
                $objCompraEstadoTipo = new compraEstadoTipo();
                $objCompraEstadoTipo->setID(1); // Estado inicial/borrador
                
                if (!$objCompraEstadoTipo->cargar()) {
                    throw new Exception("Error al cargar el tipo de estado inicial");
                }
                
                $objCompraEstado->setearSinID(
                    $nuevaCompra,
                    $objCompraEstadoTipo,
                    date('Y-m-d H:i:s'),
                    null
                );
                
                if (!$objCompraEstado->insertar()) {
                    throw new Exception("Error al crear estado inicial de la compra");
                }
                
                $compraActiva = $nuevaCompra;
            }

            // Crear y guardar el item
            $objCompraItem = new compraItem();
            $objCompraItem->setObjCompra($compraActiva);
            $objCompraItem->setObjProducto($objProducto);
            $objCompraItem->setCicantidad($datos['cicantidad']);
            
            if (!$objCompraItem->insertar()) {
                throw new Exception("Error al insertar el item en la compra");
            }

            return true;

        } catch (Exception $e) {
            error_log("Error en agregarProdCarrito: " . $e->getMessage());
            throw $e;
        }
    }

    public function buscarCompraIniciada($idUsuario) {
        try {
            // Buscar compra en estado 'iniciada' sin fecha fin para el usuario
            $objCompraEstado = new abmCompraEstado();
            $compras = $this->buscar(['idusuario' => $idUsuario]);
            $compraIniciada = null;

            foreach ($compras as $compra) {
                $estadoActual = $objCompraEstado->obtenerEstadoActual($compra->getID());
                
                // Verificar que sea estado iniciada (1) y no tenga fecha fin
                if ($estadoActual && 
                    $estadoActual['idcompraestadotipo'] == 1 && 
                    $estadoActual['cefechafin'] === null) {
                    $compraIniciada = $compra;
                    break;
                }
            }
            
            // Si no hay compra iniciada válida, crear una nueva
            if ($compraIniciada === null) {
                return $this->crearCarrito($idUsuario);
            }
            
            return $compraIniciada;
            
        } catch (Exception $e) {
            error_log("Error buscando compra iniciada: " . $e->getMessage());
            throw $e;
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

    public function crearCarrito($idUsuario) {
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
                // Buscar la compra recién creada usando el ID más reciente
                $sql = "SELECT MAX(idcompra) as ultimo_id FROM compra WHERE idusuario = " . $idUsuario;
                $base = new BaseDatos();
                $res = $base->Ejecutar($sql);
                
                if ($res && $row = $base->Registro()) {
                    $idCompra = $row['ultimo_id'];
                    
                    // Cargar la compra recién creada
                    $compra = new compra();
                    $compra->setID($idCompra);
                    if ($compra->cargar()) {
                        // Crear estado inicial (iniciada)
                        $abmCompraEstado = new abmCompraEstado();
                        $datosEstado = array(
                            'idcompra' => $idCompra,
                            'idcompraestadotipo' => 1, // ID del estado 'iniciada'
                            'cefechaini' => date('Y-m-d H:i:s'),
                            'cefechafin' => null
                        );
                        
                        error_log("Intentando crear estado inicial con datos: " . print_r($datosEstado, true));
                        
                        if ($abmCompraEstado->altaSinID($datosEstado)) {
                            error_log("Carrito creado exitosamente con ID: " . $idCompra);
                            return $compra;
                        }
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

    public function listarComprasUsuarios($param = null) {
        try {
            $where = " true ";
            if ($param !== null && isset($param['idusuario'])) {
                $where .= " AND idusuario = " . $param['idusuario'];
            }
            
            $obj = new compra();
            $arreglo = $obj->listar($where);
            error_log("Compras encontradas: " . print_r($arreglo, true));
            
            $resultado = [];
            foreach ($arreglo as $compra) {
                $abmCompraEstado = new abmCompraEstado();
                $estadoActual = $abmCompraEstado->obtenerEstadoActual($compra->getID());
                
                $objUsuario = $compra->getObjUsuario();
                
                // Solo incluir si el usuario coincide
                if ($objUsuario && $objUsuario->getID() == $param['idusuario']) {
                    $resultado[] = array(
                        'idcompra' => $compra->getID(),
                        'cofecha' => $compra->getCoFecha(),
                        'usnombre' => $objUsuario->getUsNombre(),
                        'idusuario' => $objUsuario->getID(),
                        'estado' => $estadoActual['descripcion'],
                        'idcompraestado' => $estadoActual['idcompraestado'],
                        'idcompraestadotipo' => $estadoActual['idcompraestadotipo']
                    );
                }
            }
            
            error_log("Resultado final filtrado por usuario " . $param['idusuario'] . ": " . print_r($resultado, true));
            return $resultado;
            
        } catch (Exception $e) {
            error_log("Error en listarComprasUsuarios: " . $e->getMessage());
            return [];
        }
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
            // Buscar compra en estado 'iniciada' sin fecha fin para el usuario
            $param = array(
                'idusuario' => $idUsuario,
                'estado' => 'iniciada'
            );
            
            $compras = $this->buscar($param);
            
            // Si no hay compra iniciada válida, crear una nueva
            if (empty($compras)) {
                return $this->crearCarrito($idUsuario);
            }
            
            // Verificación adicional del estado actual
            $objCompraEstado = new abmCompraEstado();
            $estadoActual = $objCompraEstado->obtenerEstadoActual($compras[0]->getID());
            
            // Si el estado actual no es 'iniciada' o tiene fecha fin, crear nuevo carrito
            if (!$estadoActual || 
                $estadoActual['idcompraestadotipo'] != 1 || 
                $estadoActual['cefechafin'] !== null) {
                return $this->crearCarrito($idUsuario);
            }
            
            return $compras[0];
            
        } catch (Exception $e) {
            error_log("Error en obtenerCarrito: " . $e->getMessage());
            throw $e;
        }
    }

    public function cancelarCarrito($data)
    {
        // Estado 4: Cancelada
        $data['idcompraestadotipo'] = 4;
        return $this->actualizarEstadoCompra($data);
    }
    
    public function aceptarCarrito($data)
    {
        try {
            $objCompraEstado = new abmCompraEstado();
            
            // Verificar que la compra esté en estado inicial
            $estadoActual = $objCompraEstado->obtenerEstadoActual($data['idcompra']);
            
            if (!$estadoActual || $estadoActual['idcompraestadotipo'] != 1) {
                throw new Exception("La compra no está en estado inicial");
            }
            
            // Cambiar el estado de la compra a "aceptada"
            return $objCompraEstado->cambiarEstado($data['idcompra'], 2);
            
        } catch (Exception $e) {
            error_log("Error en aceptarCarrito: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function enviarCarrito($data)
    {
        // Estado 3: Enviada
        $data['idcompraestadotipo'] = 3;
        return $this->actualizarEstadoCompra($data);
    }
    
    private function actualizarEstadoCompra($data)
    {
        $respuesta = false;
        $objCE = new abmCompraEstado();
        
        // Buscar el estado actual de la compra
        $list = $objCE->buscar(['idcompraestado' => $data['idcompraestado']]);
    
        if (count($list) > 0) {
            foreach ($list as $elem) {
                date_default_timezone_set('America/Argentina/Buenos_Aires');
                $idCET = $elem->getObjCompraEstadoTipo()->getID(); // Estado actual
                $fechaIni = $elem->getCeFechaIni(); // Fecha de inicio actual
                $fechaFin = date('Y-m-d H:i:s'); // Fecha fin nueva
    
                // Cambiar estado
                $respuesta = $this->cambiarEstado($data, $idCET, $fechaIni, $fechaFin, $objCE);
            }
        } else {
            throw new Exception("No se encontró un estado para la compra con el ID especificado");
        }
    
        return $respuesta;
    }
    
    public function cambiarEstado($data, $idCET, $fechaIni, $fechaFin, $objCE)
    {
        // Actualizar el estado anterior, seteando su fecha fin
        $arregloModCompra = [
            'idcompraestado' => $data['idcompraestado'],
            'idcompra' => $data['idcompra'],
            'idcompraestadotipo' => $idCET,
            'cefechaini' => $fechaIni,
            'cefechafin' => $fechaFin,
        ];
    
        // Modificar el estado actual
        $resp = $objCE->modificacion($arregloModCompra);
    
        if ($resp) {
            // Crear el nuevo estado con fecha de inicio actual y sin fecha de fin
            $arregloNewCompra = [
                'idcompra' => $data['idcompra'],
                'idcompraestadotipo' => $data['idcompraestadotipo'],
                'cefechaini' => $fechaFin,
                'cefechafin' => null,
            ];
    
            // Insertar el nuevo estado
            return $objCE->altaSinID($arregloNewCompra);
        }
    
        return false;
    }
}    