<?php

class Admin_Model_Help extends Core_Model
{
    protected $_tableAyuda; 
    
    public function __construct() {
        $this->_tableAyuda = new Application_Model_DbTable_Help();
    }    
  

      public function findAll() {
        $smt = $this->_tableAyuda->getAdapter()->select()
                        ->from($this->_tableAyuda->getName())
                        ->where("vchestado LIKE 'A'")->query();
        $result = $smt->fetchAll();
        $smt->closeCursor();
        return $result;
    }

}
