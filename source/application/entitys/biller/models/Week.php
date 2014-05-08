<?php

class Biller_Model_Week extends Core_Model
{
    protected $_table; 
    
    public function __construct() {
        $this->_table = new Application_Model_DbTable_Week();
    }
    
    public function getLastWeek() {
        $smt = $this->_table->select()
                ->order('codsema desc');
        
        //echo $smt->assemble(); exit;
        $smt = $smt->query();

        
        $result =  $smt->fetch();
        $smt->closeCursor();
        
        return $result;
    }
}

