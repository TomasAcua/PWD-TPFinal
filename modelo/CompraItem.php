<?php
class compraItem extends BaseDatos {
    private $idcompraitem;
    private $objproducto;
    private $objcompra;
    private $cicantidad;
    private $mensajeoperacion;

    public function __construct(){
        parent::__construct();
        $this->idcompraitem = "";
        $this->objproducto = new producto();
        $this->objcompra = new compra();   
        $this->cicantidad = "";
        $this->mensajeoperacion = "";
    }

    // Métodos de seteo
    public function setear($idcompraitem, $idcompra, $idproducto, $cicantidad) {
        $this->setID($idcompraitem);
        $this->setObjCompra($idcompra);
        $this->setObjProducto($idproducto);
        $this->setCiCantidad($cicantidad);
    }

    public function setearSinID($newObjProducto, $newObjCompra, $cicantidad) {
        $this->setObjProducto($newObjProducto);
        $this->setObjCompra($newObjCompra);
        $this->setCiCantidad($cicantidad);
    }

    public function setearObjetos($objProducto, $objCompra, $cicantidad) {
        try {
            if (!($objProducto instanceof producto)) {
                $this->setMensajeOperacion("Error: objProducto no es una instancia válida de producto");
                return false;
            }
            
            if (!($objCompra instanceof compra)) {
                $this->setMensajeOperacion("Error: objCompra no es una instancia válida de compra");
                return false;
            }
            
            if (!$objProducto->getID() || !$objCompra->getID()) {
                $this->setMensajeOperacion("Error: Los objetos producto o compra no tienen IDs válidos");
                return false;
            }
            
            if (!is_numeric($cicantidad) || $cicantidad <= 0) {
                $this->setMensajeOperacion("Error: Cantidad inválida");
                return false;
            }
            
            $this->setObjProducto($objProducto);
            $this->setObjCompra($objCompra);
            $this->setCiCantidad($cicantidad);
            
            return true;
            
        } catch (Exception $e) {
            $this->setMensajeOperacion("Error al setear objetos: " . $e->getMessage());
            return false;
        }
    }

    // Getters y Setters
    public function getID() {
        return $this->idcompraitem;
    }

    public function setID($valor) {
        $this->idcompraitem = $valor;
    }

    public function getObjProducto() {
        return $this->objproducto;
    }

    public function setObjProducto($newObjProducto) {
        $this->objproducto = $newObjProducto;
        return $this;
    }

    public function getObjCompra() {
        return $this->objcompra;
    }

    public function setObjCompra($newObjCompra) {
        $this->objcompra = $newObjCompra;
        return $this;
    }

    public function getCiCantidad() {
        return $this->cicantidad;
    }

    public function setCiCantidad($cicantidad) {
        $this->cicantidad = $cicantidad;
        return $this;
    }

    public function getMensajeOperacion() {
        return $this->mensajeoperacion;
    }

    public function setMensajeOperacion($valor) {
        $this->mensajeoperacion = $valor;
    }

    // Métodos de BD
    public function cargar(){
        $resp = false;
        $sql = "SELECT * FROM compraitem WHERE idcompraitem = " . $this->getID();
        if ($this->Iniciar()) {
            $res = $this->Ejecutar($sql);
            if($res > -1){
                if($res > 0){
                    $row = $this->Registro();
                    $objproducto = new producto();
                    $objcompra = new compra();

                    $objproducto->setID($row['idproducto']);
                    $objcompra->setID($row['idcompra']);

                    $objproducto->cargar();
                    $objcompra->cargar();

                    $this->setear($row['idcompraitem'], $objproducto, $objcompra, $row['cicantidad']);
                    $resp = true;
                }
            }
        } else {
            $this->setMensajeOperacion("compraitem->cargar: " . $this->getError());
        }
        return $resp;
    }

    public function insertar() {
        $resp = false;
        $sql = "INSERT INTO compraitem (idcompra, idproducto, cicantidad) VALUES (?, ?, ?)";
        
        if ($this->Iniciar()) {
            try {
                // Usamos prepare directamente desde la clase padre (PDO)
                $stmt = parent::prepare($sql);
                
                // Log de los valores antes de la inserción
                error_log("Intentando insertar compraItem:");
                error_log("idcompra: " . $this->getObjCompra()->getID());
                error_log("idproducto: " . $this->getObjProducto()->getID());
                error_log("cantidad: " . $this->getCiCantidad());
                
                $idcompra = $this->getObjCompra()->getID();
                $idproducto = $this->getObjProducto()->getID();
                $cantidad = $this->getCiCantidad();
                
                $stmt->bindParam(1, $idcompra, PDO::PARAM_INT);
                $stmt->bindParam(2, $idproducto, PDO::PARAM_INT);
                $stmt->bindParam(3, $cantidad, PDO::PARAM_INT);
                
                if ($stmt->execute()) {
                    $this->setID($this->lastInsertId());
                    $resp = true;
                    error_log("Inserción exitosa de compraItem");
                } else {
                    error_log("Error en la inserción: " . print_r($stmt->errorInfo(), true));
                    $this->setmensajeoperacion("compraItem->insertar: " . print_r($stmt->errorInfo(), true));
                }
            } catch (Exception $e) {
                error_log("Excepción en insertar compraItem: " . $e->getMessage());
                $this->setmensajeoperacion("compraItem->insertar: " . $e->getMessage());
            }
        } else {
            error_log("Error al iniciar la conexión en compraItem");
            $this->setmensajeoperacion("compraItem->insertar: No se pudo iniciar la conexión");
        }
        return $resp;
    }

    public function modificar(){
        $resp = false;
        $sql = "UPDATE compraitem 
                SET idproducto='" . $this->getObjProducto()->getID()
                . "', idcompra='" . $this->getObjCompra()->getID()
                . "', cicantidad='" . $this->getCiCantidad()
                . "' WHERE idcompraitem='" . $this->getID() . "'";
        if ($this->Iniciar()) {
            if ($this->Ejecutar($sql)) {
                $resp = true;
            } else {
                $this->setMensajeOperacion("compraitem->modificar: " . $this->getError());
            }
        } else {
            $this->setMensajeOperacion("compraitem->modificar: " . $this->getError());
        }
        return $resp;
    }

    public function eliminar(){
        $resp = false;
        $sql = "DELETE FROM compraitem WHERE idcompraitem=" . $this->getID();
        if ($this->Iniciar()) {
            if ($this->Ejecutar($sql)) {
                $resp = true;
            } else {
                $this->setMensajeOperacion("compraitem->eliminar: " . $this->getError());
            }
        } else {
            $this->setMensajeOperacion("compraitem->eliminar: " . $this->getError());
        }
        return $resp;
    }

    public function listar($condicion = "") {
        $arreglo = array();
        $sql = "SELECT * FROM compraitem";
        if ($condicion != "") {
            $sql .= " WHERE " . $condicion;
        }
        
        error_log("SQL compraItem listar: " . $sql);
        
        if ($this->Iniciar()) {
            $res = $this->Ejecutar($sql);
            if ($res > -1) {
                while ($row = $this->Registro()) {
                    error_log("Registro encontrado: " . print_r($row, true));
                    $obj = new compraItem();
                    $obj->setear(
                        $row['idcompraitem'],
                        $row['idcompra'],
                        $row['idproducto'],
                        $row['cicantidad']
                    );
                    array_push($arreglo, $obj);
                }
            }
            error_log("Total registros encontrados: " . count($arreglo));
        }
        return $arreglo;
    }
}
?>