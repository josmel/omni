<?php

class Admin_Model_VideoType extends Core_Model
{
    protected $_tableVideoType; 
    
    public function __construct() {
        $this->_tableVideoType = new Application_Model_DbTable_VideoType();
    }    
  
    public function findById($id) {
        $smt = $this->_tableVideoType->getAdapter()->select()
                ->from($this->_tableVideoType->getName())
                ->where("vchestado LIKE 'A'")
                ->where('codtvideo LIKE ?', $id)
                ->query();
        
        $result = $smt->fetchAll();
        
        if(!empty($result)) {
            $result = $result[0];
        }
        
        $smt->closeCursor();
        return $result;
    }
    
    public function getPairsAll() {
        $smt = $this->_tableVideoType->getAdapter()->select()
                ->from($this->_tableVideoType->getName())
                ->where("vchestado LIKE 'A'")->query();
        
        $result = array();
        while ($row = $smt->fetch()) {
            $result[$row['codtvideo']] = $row['nombre'];
        }
        $smt->closeCursor();
        return $result;
    }
}
