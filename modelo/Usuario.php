<?php   
class usuario extends BaseDatos{

    private $idusuario;
    private $usnombre;
    private $uspass;
    private $usmail;
    private $usdeshabilitado;
    private $mensajeOperacion;

    public function __construct(){
        parent::__construct();
        $this->idusuario="";
        $this->usnombre="";
        $this->uspass="";
        $this->usmail="";
        $this->usdeshabilitado=null;
        $this->mensajeOperacion="";
    }

    public function setear($idusuario,$usnombre,$uspass,$usmail,$usdeshabilitado){
        $this->setID($idusuario);
        $this->setUsnombre($usnombre);
        $this->setUspass($uspass);
        $this->setUsmail($usmail);
        $this->setUsdeshabilitado($usdeshabilitado);
    }

    public function setearSinID($usnombre,$uspass,$usmail,$usdeshabilitado){
        $this->setUsnombre($usnombre);
        $this->setUspass($uspass);
        $this->setUsmail($usmail);
        $this->setUsdeshabilitado($usdeshabilitado);
    }

    //MÉTODOS PROPIOS DE LA CLASE

    public function cargar(){
        $resp = false;
        $base = new BaseDatos();
        if($base->Iniciar()){
            $sql = "SELECT * FROM usuario WHERE idusuario = ".$this->getID();
            if($base->Ejecutar($sql)){
                if($row = $base->Registro()){
                    $this->setID($row['idusuario']);
                    $this->setUsNombre($row['usnombre']);
                    $this->setUsPass($row['uspass']);
                    $this->setUsMail($row['usmail']);
                    $this->setUsDeshabilitado($row['usdeshabilitado']);
                    $resp = true;
                }
            } else {
                $this->setMensajeOperacion("usuario->cargar: ".$base->getError());
            }
        } else {
            $this->setMensajeOperacion("usuario->cargar: ".$base->getError());
        }
        return $resp;
    }
    
    public function insertar(){
        $resp = false;
        
        // Si lleva ID Autoincrement, la consulta SQL no lleva dicho ID
        $sql="INSERT INTO usuario(usnombre, uspass, usmail, usdeshabilitado) 
            VALUES('"
            .$this->getUsNombre()."', '"
            .$this->getUsPass()."', '"
            .$this->getUsMail()."', '"
            .$this->getUsDeshabilitado()."'
        );";
        
        if ($this->Iniciar()) {
            if ($esteid = $this->Ejecutar($sql)) {
                // Si se usa ID autoincrement, descomentar lo siguiente:
                $this->setID($esteid);
                $resp = true;
              
            } else {
                $this->setMensajeOperacion("usuario->insertar: ".$this->getError());
            }
        } else {
            $this->setMensajeOperacion("usuario->insertar: ".$this->getError());
        }
        return $resp;
    }
    
    public function modificar(){
        $resp = false;        
        $sql="UPDATE usuario 
        SET usnombre='".$this->getUsnombre()
        ."', uspass='".$this->getUspass()
        ."', usmail='".$this->getUsmail()
        ."', usdeshabilitado='".$this->getUsdeshabilitado()
        ."' WHERE idusuario='".$this->getID()."'";
        if ($this->Iniciar()) {
            if ($this->Ejecutar($sql)) {
                $resp = true;
            } else {
                $this->setMensajeOperacion("usuario->modificar: ".$this->getError());
            }
        } else {
            $this->setMensajeOperacion("usuario->modificar: ".$this->getError());
        }
        return $resp;
    }
    
    public function eliminar(){
        $resp = false;        
        $sql="DELETE FROM usuario WHERE idusuario=".$this->getID();
        if ($this->Iniciar()) {
            if ($this->Ejecutar($sql)) {
                return true;
            } else {
                $this->setMensajeOperacion("usuario->eliminar: ".$this->getError());
            }
        } else {
            $this->setMensajeOperacion("usuario->eliminar: ".$this->getError());
        }
        return $resp;
    }
    
    public function listar($condicion = "")
    {
        $arreglo = array();
        $base = new BaseDatos();
        $sql = "SELECT * FROM usuario ";
        if ($condicion != "") {
            $sql .= ' WHERE ' . $condicion;
        }
        
        error_log("Ejecutando SQL: " . $sql);
        
        if ($base->Iniciar()) {
            $res = $base->Ejecutar($sql);
            error_log("Resultado de la ejecución: " . $res);
            
            if ($res > -1) {
                if ($res > 0) {
                    while ($row = $base->Registro()) {
                        error_log("Registro encontrado: " . print_r($row, true));
                        $obj = new usuario();
                        $obj->setear($row['idusuario'], $row['usnombre'], $row['uspass'], $row['usmail'], $row['usdeshabilitado']);
                        array_push($arreglo, $obj);
                    }
                } else {
                    error_log("No se encontraron registros");
                }
            } else {
                error_log("Error en la consulta");
            }
        } else {
            error_log("Error al iniciar la base de datos: " . $base->getError());
        }
        
        return $arreglo;
    }


    /**
     * Get the value of idusuario
     */ 
    public function getID()
    {
        return $this->idusuario;
    }

    /**
     * Set the value of idusuario
     *
     * @return  self
     */ 
    public function setID($idusuario)
    {
        $this->idusuario = $idusuario;

        return $this;
    }

    /**
     * Get the value of usnombre
     */ 
    public function getUsNombre()
    {
        return $this->usnombre;
    }

    /**
     * Set the value of usnombre
     *
     * @return  self
     */ 
    public function setUsNombre($usnombre)
    {
        $this->usnombre = $usnombre;

        return $this;
    }

    /**
     * Get the value of uspass
     */ 
    public function getUsPass()
    {
        return $this->uspass;
    }

    /**
     * Set the value of uspass
     *
     * @return  self
     */ 
    public function setUsPass($uspass)
    {
        $this->uspass = $uspass;

        return $this;
    }

    /**
     * Get the value of usdeshabilitado
     */ 
    public function getUsDeshabilitado()
    {
        return $this->usdeshabilitado;
    }

    /**
     * Set the value of usdeshabilitado
     *
     * @return  self
     */ 
    public function setUsDeshabilitado($usdeshabilitado)
    {
        $this->usdeshabilitado = $usdeshabilitado;

        return $this;
    }

    /**
     * Get the value of mensajeOperacion
     */ 
    public function getMensajeOperacion()
    {
        return $this->mensajeOperacion;
    }

    /**
     * Set the value of mensajeOperacion
     *
     * @return  self
     */ 
    public function setMensajeOperacion($mensajeOperacion)
    {
        $this->mensajeOperacion = $mensajeOperacion;

        return $this;
    }

    /**
     * Get the value of usmail
     */ 
    public function getUsMail()
    {
        return $this->usmail;
    }

    /**
     * Set the value of usmail
     *
     * @return  self
     */ 
    public function setUsMail($usmail)
    {
        $this->usmail = $usmail;

        return $this;
    }
}    


?>