<?php

class producto extends BaseDatos
{
    //ver los diferentes estados de la compra y sus posibles contextos de cambio
    //hacer la extensión con la BD

    private $idproducto;
    private $pronombre; 
    private $prodetalle;
    private $procantstock;
    private $precio;
    private $prodeshabilitado;
    private $imagen;
    private $mensajeoperacion;

    public function __construct()
    {
        parent:: __construct();
        $this->idproducto="";
        $this->pronombre="";
        $this->prodetalle="";
        $this->procantstock="";
        $this->precio="";
        $this->prodeshabilitado="";
        $this->imagen="";
        $this->mensajeoperacion="";
    }

    public function setear($idproducto, $pronombre, $prodetalle, $procantstock, $precio, $prodeshabilitado, $imagen)
    {
        $this->setID($idproducto);
        $this->setProNombre($pronombre);
        $this->setProDetalle($prodetalle);
        $this->setProCantStock($procantstock);
        $this->setPrecio($precio);
        $this->setProDeshabilitado($prodeshabilitado);
        $this->setImagen($imagen);
    }

    public function setearSinID($pronombre, $prodetalle, $procantstock, $precio, $prodeshabilitado, $imagen)
    {
        $this->setProNombre($pronombre);
        $this->setProDetalle($prodetalle);
        $this->setProCantStock($procantstock);
        $this->setPrecio($precio);
        $this->setProDeshabilitado($prodeshabilitado);
        $this->setImagen($imagen);
    }


    //MÉTODOS PROPIOS DE LA CLASE

    public function cargar()
    {
        $resp = false;
        
        $sql="SELECT * FROM producto WHERE idproducto = ".$this->getID();
        if ($this->Iniciar()) {
            $res = $this->Ejecutar($sql);
            if ($res>-1) {
                if ($res>0) {
                    $row = $this->Registro();
                    $this->setear($row['idproducto'], $row['pronombre'], $row['prodetalle'], $row['procantstock'], $row['precio'], $row['prodeshabilitado'], $row['imagen']);
                }
            }
        } else {
            $this->setMensajeOperacion("producto->listar: ".$this->getError());
        }
        return $resp;
    }

    public function insertar()
    {
        $resp = false;
        error_log("Intentando insertar producto");
        
        if ($this->Iniciar()) {
            $sql = "INSERT INTO producto(pronombre, prodetalle, procantstock, precio, prodeshabilitado, imagen) 
                    VALUES ('" . 
                    $this->getPronombre() . "', '" . 
                    $this->getProdetalle() . "', " . 
                    $this->getProCantStock() . ", " . 
                    $this->getPrecio() . ", " . 
                    ($this->getProDeshabilitado() ? "'" . $this->getProDeshabilitado() . "'" : "NULL") . ", '" . 
                    $this->getImagen() . "')";
                    
            error_log("SQL a ejecutar: " . $sql);
            
            if ($esteid = $this->Ejecutar($sql)) {
                $this->setID($esteid);
                $resp = true;
                error_log("Inserción exitosa. ID: " . $esteid);
            } else {
                $error = $this->getError();
                error_log("Error en inserción: " . $error);
                $this->setMensajeOperacion("producto->insertar: " . $error);
            }
        } else {
            $error = $this->getError();
            error_log("Error al iniciar conexión: " . $error);
            $this->setMensajeOperacion("producto->insertar: " . $error);
        }
        return $resp;
    }

    public function modificar()
    {
        $resp = false;
        
        $sql="UPDATE producto 
        SET pronombre='".$this->getProNombre()
        ."', prodetalle='".$this->getProDetalle()
        ."', procantstock='". $this->getProCantStock()
        ."', precio='". $this->getPrecio()
        ."', prodeshabilitado='". $this->getProDeshabilitado()
        ."', imagen='". $this->getImagen()
        ."' WHERE idproducto='".$this->getID()."'";
        if ($this->Iniciar()) {
            if ($this->Ejecutar($sql)) {
                $resp = true;
            } else {
                $this->setMensajeOperacion("producto->modificar: ".$this->getError());
            }
        } else {
            $this->setMensajeOperacion("producto->modificar: ".$this->getError());
        }
        return $resp;
    }

    public function eliminar()
    {
        $resp = false;
        error_log("Intentando deshabilitar producto ID: " . $this->getID());
        
        if ($this->Iniciar()) {
            $sql = "UPDATE producto SET prodeshabilitado = NOW() WHERE idproducto = " . $this->getID();
            error_log("SQL a ejecutar: " . $sql);
            
            $resultado = $this->Ejecutar($sql);
            if ($resultado > 0) {
                $resp = true;
                error_log("Deshabilitación exitosa");
            } else {
                $error = $this->getError();
                error_log("Error en deshabilitación: " . $error);
                $this->setMensajeOperacion("producto->eliminar: " . $error);
            }
        } else {
            $error = $this->getError();
            error_log("Error al iniciar conexión: " . $error);
            $this->setMensajeOperacion("producto->eliminar: " . $error);
        }
        return $resp;
    }

    public function listar($parametro="")
    {
        $arreglo = array();
        
        $sql="SELECT * FROM producto ";
        if ($parametro!="") {
            $sql.='WHERE '.$parametro;
        }
        $res = $this->Ejecutar($sql);
        if ($res>-1) {
            if ($res>0) {
                while ($row = $this->Registro()) {
                    $producto = new producto();
                    $producto->setear($row['idproducto'], $row['pronombre'], $row['prodetalle'], $row['procantstock'], $row['precio'], $row['prodeshabilitado'], $row['imagen']);
                    array_push($arreglo, $producto);
                }
            }
        } else {
            $this->setMensajeOperacion("producto->listar: ".$this->getError());
        }

        return $arreglo;
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
     * Get the value of idproducto
     */ 
    public function getID()
    {
        return $this->idproducto;
    }

    /**
     * Set the value of idproducto
     *
     * @return  self
     */ 
    public function setID($idproducto)
    {
        $this->idproducto = $idproducto;

        return $this;
    }

    /**
     * Get the value of pronombre
     */ 
    public function getProNombre()
    {
        return $this->pronombre;
    }

    /**
     * Set the value of pronombre
     *
     * @return  self
     */ 
    public function setProNombre($pronombre)
    {
        $this->pronombre = $pronombre;

        return $this;
    }

    /**
     * Get the value of prodetalle
     */ 
    public function getProDetalle()
    {
        return $this->prodetalle;
    }

    /**
     * Set the value of prodetalle
     *
     * @return  self
     */ 
    public function setProDetalle($prodetalle)
    {
        $this->prodetalle = $prodetalle;

        return $this;
    }

    /**
     * Get the value of procantstock
     */ 
    public function getProCantStock()
    {
        return $this->procantstock;
    }

    /**
     * Set the value of procantstock
     *
     * @return  self
     */ 
    public function setProCantStock($procantstock)
    {
        $this->procantstock = $procantstock;

        return $this;
    }

    /**
     * Get the value of precio
     */ 
    public function getPrecio()
    {
        return $this->precio;
    }

    /**
     * Set the value of precio
     *
     * @return  self
     */ 
    public function setPrecio($precio)
    {
        $this->precio = $precio;

        return $this;
    }

    /**
     * Get the value of prodeshabilitado
     */ 
    public function getProDeshabilitado()
    {
        return $this->prodeshabilitado;
    }

    /**
     * Set the value of prodeshabilitado
     *
     * @return  self
     */ 
    public function setProDeshabilitado($prodeshabilitado)
    {
        $this->prodeshabilitado = $prodeshabilitado;

        return $this;
    }
     /**
     * Get the value of prodeshabilitado
     */ 
    public function getImagen()
    {
        return $this->imagen;
    }

    /**
     * Set the value of prodeshabilitado
     *
     * @return  self
     */ 
    public function setImagen($imagen)
    {
        $this->imagen = $imagen;

        return $this;
    }

}
