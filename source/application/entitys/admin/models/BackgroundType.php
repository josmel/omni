<?php

class Admin_Model_BackgroundType extends Core_Model
{
    protected $_tableBackgroundType; 
    
    public function __construct() {
        $this->_tableBackgroundType = new Application_Model_DbTable_BackgroundType();
    }    
  
    public function findById($id) {
        $smt = $this->_tableBackgroundType->getAdapter()->select()
                ->from($this->_tableBackgroundType->getName())
                ->where("vchestado LIKE 'A'")
                ->where("codtfondo LIKE ?", $id)
                ->query();
        
        $result = $smt->fetchAll();
        
        if(!empty($result)) {
            $result = $result[0];
        }
        $smt->closeCursor();
        return $result;
    }
    
    public function getPairsAll() {
        $smt = $this->_tableBackgroundType->getAdapter()->select()
                ->from($this->_tableBackgroundType->getName())
                ->where("vchestado LIKE 'A'")->query();
        
        $result = array();
        while ($row = $smt->fetch()) {
            $result[$row['codtfondo']] = $row['nombre'];
        }
        $smt->closeCursor();
        return $result;
    }
    
    public function findBCById($id) {
        $smt = $this->_tableBackgroundType->getAdapter()->select()
                ->from($this->_tableBackgroundType->getName())
                ->where("vchestado LIKE 'C'")
                ->where("codtfondo LIKE ?", $id)
                ->query();
        
        $result = $smt->fetchAll();
        
        if(!empty($result)) {
            $result = $result[0];
        }
        $smt->closeCursor();
        return $result;
    }
    
    public function updateName($id, $name) {
        $data = array('nombre' => $name);
        $where = $this->_tableBackgroundType->getAdapter()->quoteInto("codtfondo = ?", $id); 
        
        return $this->_tableBackgroundType->update($data, $where);
    }
}
