<?php

class Admin_Model_FileType extends Core_Model
{
    protected $_tableFileType; 
    
    public function __construct() {
        $this->_tableFileType = new Application_Model_DbTable_FileType();
    }    
  
    public function findById($id) {
        $smt = $this->_tableFileType->getAdapter()->select()
                ->from($this->_tableFileType->getName())
                ->where("vchestado LIKE 'A'")
                ->where('codtfile LIKE ?', $id)
                ->query();
        
        $result = $smt->fetchAll();
        
        if(!empty($result)) {
            $result = $result[0];
        }
        $smt->closeCursor();
        return $result;
    }
    
    public function getPairsAll() {
        $smt = $this->_tableFileType->getAdapter()->select()
                ->from($this->_tableFileType->getName())
                ->where("vchestado LIKE 'A'")->query();
        
        $result = array();
        while ($row = $smt->fetch()) {
            $result[$row['codtfile']] = $row['nombre'];
        }
        $smt->closeCursor();
        return $result;
    }
     public function findAllByType() {
        $select = $this->_tableBanner->getAdapter()->select()
                ->from(array('b' => $this->_tableFileType->getName()), 
                       array('b.codtfile', 'b.nombre')
                )->join(array('tb' => 'tfile')
                )->where("b.codtbanner LIKE ?", $idType)
                ->order("b.norder ASC");
        if (!$visible) $select->where("b.vchestado IN ('A', 'I')");
        else $select->where("b.vchestado LIKE 'A'");
      
        $select = $select->query();
        $result = $select->fetchAll();
        $select->closeCursor();
        
        return $result;
    }
    
     public function getCodAll() {
        $smt = $this->_tableFileType->getAdapter()->select()->distinct()
                        ->from($this->_tableFileType->getName())
                         ->where("vchestado LIKE 'A'")->query();
        $result = $smt->fetchAll();
        $smt->closeCursor();
        return $result;
    }
}
