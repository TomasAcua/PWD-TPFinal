<?php

class Session
{
    public function __construct()
    {
        if (!session_start()) {
            return false;
        } else {
            return true;
        }
    }

    public function iniciar($nombreUsuario, $pswUsuario)
    {
        error_log("=== INICIO DE MÉTODO INICIAR ===");
        $resp = false;
        
        try {
            if (!$this->activa()) {
                error_log("No hay sesión activa, procediendo con la validación");
                
                // Validamos las credenciales
                if ($this->validar($nombreUsuario, $pswUsuario)) {
                    error_log("Validación exitosa, iniciando sesión");
                    
                    // Iniciamos la sesión
                    if (session_status() !== PHP_SESSION_ACTIVE) {
                        error_log("Iniciando sesión PHP");
                        session_start();
                    }
                    
                    $_SESSION['usnombre'] = $nombreUsuario;
                    
                    // Obtenemos el objeto usuario
                    $objAbmUsuario = new abmUsuario();
                    $param = ["usnombre" => $nombreUsuario];
                    $listaUsuario = $objAbmUsuario->buscar($param);
                    
                    error_log("Buscando datos de usuario");
                    
                    if (!empty($listaUsuario)) {
                        $user = $listaUsuario[0];
                        error_log("Usuario encontrado, ID: " . $user->getID());
                        
                        $_SESSION['idusuario'] = $user->getID();
                        $_SESSION['usmail'] = $user->getUsMail();
                        $_SESSION['usdeshabilitado'] = $user->getUsDeshabilitado();
                        
                        // Intentamos setear el rol activo
                        if ($this->setearRolActivo()) {
                            error_log("Rol activo seteado correctamente");
                        } else {
                            error_log("No se pudo setear el rol activo");
                        }
                        
                        $resp = true;
                        error_log("Sesión iniciada correctamente");
                    } else {
                        error_log("No se encontró el usuario en la base de datos");
                    }
                } else {
                    error_log("Falló la validación de credenciales");
                }
            } else {
                error_log("Ya existe una sesión activa");
            }
        } catch (Exception $e) {
            error_log("Error en iniciar(): " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
        }
        
        error_log("Resultado de iniciar(): " . ($resp ? 'EXITOSO' : 'FALLIDO'));
        error_log("=== FIN DE MÉTODO INICIAR ===");
        
        return $resp;
    }

    public function setearRolActivo()
    {
        error_log("=== INICIO SETEAR ROL ACTIVO ===");
        $verificador = false;
        
        try {
            $rolesUs = $this->getRoles();
            error_log("Roles encontrados: " . count($rolesUs));
            
            if (count($rolesUs) > 0) {
                $rolActivoDescripcion = $rolesUs[0]->getRolDescripcion();
                error_log("Rol activo descripción: " . $rolActivoDescripcion);
                
                $_SESSION['rolactivodescripcion'] = $rolActivoDescripcion;
                $idRol = $this->buscarIdRol($rolActivoDescripcion);
                error_log("ID del rol: " . $idRol);
                
                $_SESSION['rolactivoid'] = $idRol;
                $verificador = true;
                error_log("Rol activo seteado correctamente");
            } else {
                error_log("No se encontraron roles para el usuario");
                $_SESSION['rolactivodescripcion'] = null;
                $_SESSION['rolactivoid'] = null;
                // Aún consideramos exitoso el seteo si no hay roles
                $verificador = true;
            }
        } catch (Exception $e) {
            error_log("Error en setearRolActivo: " . $e->getMessage());
        }
        
        error_log("Resultado setearRolActivo: " . ($verificador ? 'EXITOSO' : 'FALLIDO'));
        error_log("=== FIN SETEAR ROL ACTIVO ===");
        
        return $verificador;
    }

    public function buscarIdRol($param)
    {
        $retorno = null;
        $roles = $this->getRoles();
        foreach ($roles as $rol) {
            if ($rol->getRolDescripcion() === $param) {
                $retorno = $rol->getID();
            }
        }

        return $retorno;
    }

    public function activa()
    {
        if (session_status() === PHP_SESSION_ACTIVE 
            && isset($_SESSION['usnombre']) 
            && !empty($_SESSION['usnombre'])
            && isset($_SESSION['idusuario'])) {
            return true;
        }
        return false;
    }

    public function sesionActiva()
    {
        $resp = false;
        if ($this->getNombreUsuarioLogueado() <> null) {
            $resp = true;
        }
        return $resp;
    }

    public function validar($usNombre, $usPsw)
    {
        error_log("=== INICIO DE VALIDACIÓN ===");
        error_log("Validando usuario: " . $usNombre);
        error_log("Hash recibido: " . $usPsw);
        
        $resp = false;
        try {
            $objAbmUsuario = new abmUsuario();
            
            // Primero verificamos si el usuario existe
            $paramUsuario = ["usnombre" => $usNombre];
            $userCheck = $objAbmUsuario->buscar($paramUsuario);
            
            error_log("Búsqueda de usuario - Parámetros: " . print_r($paramUsuario, true));
            error_log("Usuario encontrado: " . (!empty($userCheck) ? 'SI' : 'NO'));
            
            if (!empty($userCheck)) {
                $usuarioEncontrado = $userCheck[0];
                error_log("Password almacenado: " . $usuarioEncontrado->getUsPass());
                error_log("Password recibido: " . $usPsw);
                
                // Ahora verificamos la contraseña
                if ($usuarioEncontrado->getUsPass() === $usPsw) {
                    error_log("Contraseña correcta");
                    $resp = true;
                } else {
                    error_log("Contrasea incorrecta");
                }
            } else {
                error_log("Usuario no encontrado en la base de datos");
            }
            
        } catch (Exception $e) {
            error_log("Error en validación: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
        }
        
        error_log("Resultado de validación: " . ($resp ? 'EXITOSO' : 'FALLIDO'));
        error_log("=== FIN DE VALIDACIÓN ===\n");
        
        return $resp;
    }


    private function getUsuario()
{
    $user = null;
    if ($this->activa() && isset($_SESSION['usnombre'])) {
        $objAbmUsuario = new AbmUsuario();
        $param['usnombre'] = $_SESSION['usnombre'];
        $listaUsuario = $objAbmUsuario->buscar($param);
        // Añadir verificación antes de acceder al índice 0
        if (!empty($listaUsuario)) {
            $user = $listaUsuario[0];
        }
    }
    return $user;
}

    public function obtenerDeshabilitado($fecha)
    {
        $retorno = false;
        if ($fecha === null || $fecha === '0000-00-00 00:00:00') {
            $retorno = true;
        }
        return $retorno;
    }

    public function getRoles()
    {
        //Devuelve un arreglo con los objetos rol del user
        $roles = [];
        $user = $this->getUsuario();
        if ($user != null) {
            //Primero busco la instancia de UsuarioRol
            $objAbmUsuarioRol = new AbmUsuarioRol();
            //Creo el parametro con el id del usuario
            $parametroUser = array('idusuario' => $user->getID());
            $listaUsuarioRol = $objAbmUsuarioRol->buscar($parametroUser);
            foreach ($listaUsuarioRol as $tupla) {
                array_push($roles, $tupla->getObjRol());
            }
        }
        return $roles;
    }

    public function getNombreUsuarioLogueado()
    {
        return isset($_SESSION['usnombre']) ? $_SESSION['usnombre'] : '';
    }

    public function getIDUsuarioLogueado()
    {
        //retorna el id del usuario logueado
        $nombreUsuario = null;
        if (isset($_SESSION['idusuario'])) {
            $nombreUsuario = $_SESSION['idusuario'];
        }
        return $nombreUsuario;
    }

    public function getMailUsuarioLogueado()
    {
        //retorna el mail del usuario logueado
        $nombreUsuario = null;
        if (isset($_SESSION['usmail'])) {
            $nombreUsuario = $_SESSION['usmail'];
        }
        return $nombreUsuario;
    }

    public function getRolActivo()
    {
        $resp = [];
        if (isset($_SESSION['rolactivodescripcion']) && isset($_SESSION['rolactivoid'])) {
            $resp = [
                'rol' => $_SESSION['rolactivodescripcion'],
                'id' => $_SESSION['rolactivoid']
            ];
            
            // Debug
            error_log("Rol activo: " . print_r($resp, true));
        }
        return $resp;
    }


    public function cerrar()
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
        }
    }

    public function setIdRolActivo($param)
    {
        $_SESSION['rolactivoid'] = $param;
    }

    public function setDescripcionRolActivo($param)
    {
        $_SESSION['rolactivodescripcion'] = $param;
    }


    public function verificarPermiso($param)
    {
        error_log("=== INICIO verificarPermiso ===");
        error_log("Verificando acceso a: " . $param);
        
        $permiso = false;
        
        if ($this->activa()) {
            $rolActivo = $this->getRolActivo();
            error_log("Rol activo: " . print_r($rolActivo, true));
            
            if ($rolActivo) {
                $objMR = new abmMenuRol();
                $listaMR = $objMR->buscar(['idrol' => $rolActivo['id']]);
                error_log("Resultado búsqueda menús: " . print_r($listaMR, true));
                
                // Verificar si $listaMR es array y no está vacío
                if (is_array($listaMR) && !empty($listaMR)) {
                    error_log("Menús encontrados para rol: " . count($listaMR));
                    
                    foreach ($listaMR as $menuRol) {
                        $menu = $menuRol->getObjMenu();
                        if (!$menu) {
                            error_log("Menú no encontrado para menuRol");
                            continue;
                        }
                        
                        error_log("Verificando menú: " . $menu->getMeNombre() . " - Ruta: " . $menu->getMeDescripcion());
                        
                        // Si es menú padre
                        if ($menu->getMeDescripcion() === '#') {
                            $abmMenu = new abmMenu();
                            $hijos = $abmMenu->tieneHijos($menu->getID());
                            
                            if (is_array($hijos) && !empty($hijos)) {
                                error_log("Menú padre encontrado, verificando " . count($hijos) . " hijos");
                                
                                foreach ($hijos as $hijo) {
                                    $rutaHijo = $this->normalizarRuta($hijo->getMeDescripcion());
                                    $rutaParam = $this->normalizarRuta($param);
                                    error_log("Comparando rutas - Hijo: '$rutaHijo' con Param: '$rutaParam'");
                                    
                                    if ($rutaHijo === $rutaParam) {
                                        error_log("¡Coincidencia encontrada!");
                                        $permiso = true;
                                        break 2;
                                    }
                                }
                            } else {
                                error_log("No se encontraron hijos para el menú padre");
                            }
                        } else {
                            $rutaMenu = $this->normalizarRuta($menu->getMeDescripcion());
                            $rutaParam = $this->normalizarRuta($param);
                            error_log("Comparando rutas - Menu: '$rutaMenu' con Param: '$rutaParam'");
                            
                            if ($rutaMenu === $rutaParam) {
                                error_log("¡Coincidencia encontrada!");
                                $permiso = true;
                                break;
                            }
                        }
                    }
                } else {
                    error_log("No se encontraron menús para el rol " . $rolActivo['id']);
                }
            } else {
                error_log("No hay rol activo");
            }
        } else {
            error_log("Sesión no activa");
        }
        
        error_log("Resultado final: " . ($permiso ? "PERMITIDO" : "DENEGADO"));
        error_log("=== FIN verificarPermiso ===");
        return $permiso;
    }

    private function normalizarRuta($ruta)
    {
        if ($ruta === '#') return $ruta;
        
        // Convertir a minúsculas
        $ruta = strtolower($ruta);
        
        // Eliminar '/TPFinal/Vista/' si existe
        $ruta = str_replace('/tpfinal/vista/', '', $ruta);
        
        // Eliminar './' si existe al principio
        $ruta = str_replace('./', '', $ruta);
        
        // Eliminar barras iniciales y finales
        $ruta = trim($ruta, '/');
        
        error_log("Ruta normalizada: '$ruta'");
        return $ruta;
    }

    public function cambiarRol($datos)
    {
        $resp = false;
        $rolActivo = $this->getRolActivo();

        if ($rolActivo['rol'] <> $datos['nuevorol']) { // SI EL ROL ES DISTINTO AL YA SETEADO HACEMOS EL CAMBIO
            $idRol = $this->buscarIdRol($datos['nuevorol']);
            $this->setIdRolActivo($idRol);
            $this->setDescripcionRolActivo($datos['nuevorol']);
            $resp = true;
        }

        return $resp;
    }
}

