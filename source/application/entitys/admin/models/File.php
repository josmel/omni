<?php

class Admin_Model_File extends Core_Model
{
    protected $_tableFile; 
    
    public function __construct() {
        $this->_tableFile = new Application_Model_DbTable_File();
    }    
 
  
    
       public function findAll($cod) {
             $select = $this->_tableFile->getAdapter()->select()
                ->from(array('f' => $this->_tableFile->getName()), 
                       array('f.idfile',  'f.titulo', 
                             'f.extfile', 'f.nombre','f.descripcion')
                )->join(array('tf' => 'ttipofile'), 
                        "f.codtfile = tf.codtfile",
                            array('codproy' => 'tf.codproy')
                )->where("f.vchestado LIKE 'A'")//;
                 ->where("f.codtfile = ?", $cod);
        $select = $select->query();
        $result = $select->fetchAll();
        $select->closeCursor();
        return $result;
    }
    
    
    public function countFile($codFile) {
             $select = $this->_tableFile->getAdapter()->select()
                ->from(array('f' => $this->_tableFile->getName()),
                        array('cantidad'=>'COUNT(f.codtfile)')
                )->join(array('tf' => 'ttipofile'), 
                        "f.codtfile = tf.codtfile",
                            array('codproy' => 'tf.codproy')
                )->where("f.vchestado LIKE 'A'")//;
                 ->where("f.codtfile = ?", $codFile)
                      ->group("f.codtfile");
        $select = $select->query();
        $result = $select->fetchAll();
        $select->closeCursor();
        return $result;
    }
   public function getCodId($cod) {
        $smt = $this->_tableFile->getAdapter()->select()->distinct()
                        ->from($this->_tableFile->getName())
                       ->where("idfile = ?", $cod)->query();
        $result = $smt->fetch();
        $smt->closeCursor();
        return $result;
    }
   
    public function getCodAll() {
        $smt = $this->_tableFile->getAdapter()->select()->distinct()
                        ->from($this->_tableFile->getName(),'codtfile')
                         ->where("vchestado LIKE 'A'")->query();
        $result = $smt->fetchAll();
        $smt->closeCursor();
        return $result;
    }
}
