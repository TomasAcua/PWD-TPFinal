<?php

class compra extends BaseDatos
{

    private $idcompra;
    private $cofecha; //TIMESTAMP
    private $idusuario;
    private $objUsuario;
    private $mensajeoperacion;

    public function __construct()
    {
        parent::__construct();
        $this->idcompra = null;
        $this->cofecha = "";
        $this->idusuario = null;
        $this->objUsuario = null;
        $this->mensajeoperacion = "";
    }

    public function setear($idcompra, $cofecha, $idusuario)
    {
        $this->setIdcompra($idcompra);
        $this->setCofecha($cofecha);
        $this->setIdusuario($idusuario);
        
        // Crear objeto usuario si recibimos el ID
        if ($idusuario != null) {
            $objUsuario = new usuario();
            $objUsuario->setID($idusuario);
            $objUsuario->cargar(); // Cargar datos del usuario
            $this->setObjUsuario($objUsuario);
        }
    }

    public function setearSinID($cofecha, $objUsuario){
        $resp = false;
        if ($objUsuario != null && $objUsuario instanceof usuario) {
            $this->setCofecha($cofecha);
            $this->setIdusuario($objUsuario->getID());
            $this->setObjUsuario($objUsuario);
            $resp = true;
        }
        return $resp;
    }


    //MÉTODOS PROPIOS DE LA CLASE

    public function cargar()
    {
        $resp = false;
        $sql = "SELECT * FROM compra WHERE idcompra = " . $this->getID();
        if ($this->Iniciar()) {
            $res = $this->Ejecutar($sql);
            if ($res > -1) {
                if ($res > 0) {
                    $row = $this->Registro();
                    
                    // Crear y cargar objeto usuario
                    $objUsuario = new usuario();
                    $objUsuario->setID($row['idusuario']);
                    $objUsuario->cargar();
                    
                    $this->setear($row['idcompra'], $row['cofecha'], $row['idusuario']);
                    $resp = true;
                }
            }
        } else {
            $this->setMensajeOperacion("compra->cargar: " . $this->getError());
        }
        return $resp;
    }

    public function insertar()
    {
        $resp = false;
        $sql = "INSERT INTO compra (cofecha, idusuario) 
                VALUES ('".$this->getCofecha()."', ".$this->getIdusuario().")";
        
        if ($this->Iniciar()) {
            if ($elid = $this->Ejecutar($sql)) {
                $this->setIdcompra($elid);
                $resp = true;
            } else {
                $this->setmensajeoperacion("compra->insertar: ".$this->getError());
            }
        } else {
            $this->setmensajeoperacion("compra->insertar: ".$this->getError());
        }
        return $resp;
    }

    public function modificar()
    {
        $resp = false;
        
        // Verificar que tenemos un objeto usuario válido
        if ($this->getObjUsuario() == null) {
            throw new Exception("Error: Se requiere un usuario válido para modificar la compra");
        }
        
        $sql = "UPDATE compra SET 
                cofecha='" . $this->getCofecha() . "', 
                idusuario=" . $this->getObjUsuario()->getID() . "
                WHERE idcompra=" . $this->getID();
        
        if ($this->Iniciar()) {
            if ($this->Ejecutar($sql)) {
                $resp = true;
            } else {
                $this->setMensajeOperacion("compra->modificar: " . $this->getError());
            }
        } else {
            $this->setMensajeOperacion("compra->modificar: " . $this->getError());
        }
        return $resp;
    }

    public function eliminar()
    {
        $resp = false;
        $sql = "DELETE FROM compra WHERE idcompra=" . $this->getID();
        if ($this->Iniciar()) {
            if ($this->Ejecutar($sql)) {
                return true;
            } else {
                $this->setMensajeOperacion("compra->eliminar: " . $this->getError());
            }
        } else {
            $this->setMensajeOperacion("compra->eliminar: " . $this->getError());
        }
        return $resp;
    }

    public function listar($parametro = "")
    {
        $arreglo = array();
        $base = new BaseDatos();
        $sql = "SELECT * FROM compra ";
        if ($parametro != "") {
            $sql .= " WHERE " . $parametro;
        }
        error_log("SQL en compra->listar: " . $sql);
        
        if ($base->Iniciar()) {
            if ($base->Ejecutar($sql)) {
                while ($row2 = $base->Registro()) {
                    $obj = new compra();
                    $obj->setear($row2['idcompra'], $row2['cofecha'], $row2['idusuario']);
                    array_push($arreglo, $obj);
                }
            } else {
                error_log("Error en compra->listar: " . $base->getError());
            }
        } else {
            error_log("Error al iniciar base de datos en compra->listar");
        }
        return $arreglo;
    }

    public function buscar($param) {
        $where = " true ";
        if ($param) {
            if (isset($param['sql'])) {
                // Si se proporciona una consulta SQL completa, usarla directamente
                $sql = $param['sql'];
            } else {
                if (isset($param['idusuario'])) {
                    $where .= " AND idusuario = " . $param['idusuario'];
                }
                // ... otros parámetros de búsqueda ...
                
                $sql = "SELECT * FROM compra WHERE $where";
            }
        }
        
        $arreglo = array();
        if ($this->Iniciar()) {
            if ($this->Ejecutar($sql)) {
                while ($row = $this->Registro()) {
                    $obj = new compra();
                    $obj->setear($row['idcompra'], $row['cofecha'], $row['idusuario']);
                    array_push($arreglo, $obj);
                }
            } else {
                $this->setmensajeoperacion("compra->buscar: " . $this->getError());
            }
        } else {
            $this->setmensajeoperacion("compra->buscar: " . $this->getError());
        }
        return $arreglo;
    }




    /**
     * Get the value of idcompra
     */
    public function getID()
    {
        return $this->idcompra;
    }

    /**
     * Set the value of idcompra
     *
     * @return  self
     */
    public function setID($idcompra)
    {
        $this->idcompra = $idcompra;

        return $this;
    }

    /**
     * Get the value of cofecha
     */
    public function getCofecha()
    {
        return $this->cofecha;
    }

    /**
     * Set the value of cofecha
     *
     * @return  self
     */
    public function setCofecha($cofecha)
    {
        $this->cofecha = $cofecha;

        return $this;
    }

    /**
     * Get the value of idusuario
     */
    public function getIdUsuario()
    {
        return $this->idusuario;
    }

    /**
     * Set the value of idusuario
     *
     * @return  self
     */
    public function setIdUsuario($idusuario)
    {
        $this->idusuario = $idusuario;
    }

    /**
     * Get the value of objUsuario
     */
    public function getObjUsuario()
    {
        if ($this->objUsuario == null && $this->idusuario !== null) {
            $objUsuario = new usuario();
            $objUsuario->setID($this->idusuario);
            if ($objUsuario->cargar()) {
                $this->objUsuario = $objUsuario;
            }
        }
        return $this->objUsuario;
    }

    /**
     * Set the value of objUsuario
     *
     * @return  self
     */
    public function setObjUsuario($objUsuario)
    {
        $this->objUsuario = $objUsuario;
    }

    /**
     * Get the value of mensajeoperacion
     */
    public function getMensajeOperacion()
    {
        return $this->mensajeoperacion;
    }

    /**
     * Set the value of mensajeoperacion
     *
     * @return  self
     */
    public function setMensajeOperacion($mensajeoperacion)
    {
        $this->mensajeoperacion = $mensajeoperacion;

        return $this;
    }

    /**
     * @param int $idcompra
     */
    public function setIdcompra($idcompra) {
        $this->idcompra = $idcompra;
    }

    public function getIdcompra() {
        return $this->idcompra;
    }
}
