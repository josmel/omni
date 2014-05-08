<?php

class Shop_Model_Discount extends Core_Model {
    protected $_tableDiscount; 
    
    public function __construct() {
        parent::__construct();
        $this->_tableDiscount = new Application_Model_DbTable_Discount();
    }
    
    public function findAllByBusinessmanType($idBusinessmanType) {
        $smt = $this->_tableDiscount->getAdapter()->select()
                ->from(Application_Model_DbTable_Discount::NAMETABLE)
                ->where('codtemp LIKE ?', $idBusinessmanType)
                ->where('vchestado LIKE ?', 'A')->order('pordesc ASC');
                
        //echo $smt->assemble(); exit;
        $smt = $smt->query();
        
        $result = $smt->fetchAll();
        
        $smt->closeCursor();
        
        return $result;
    }
    
    public function findByPoints($idBusinessmanType, $points) {
        $smt = $this->_tableDiscount->getAdapter()->select()
                ->from(Application_Model_DbTable_Discount::NAMETABLE)
                ->where('codtemp LIKE ?', $idBusinessmanType)
                ->where('pindesc <= ?', $points)
                ->where('pfidesc >= ?', $points)
                ->where('vchestado LIKE ?', 'A');
                
        //echo $smt->assemble(); exit;
        $smt = $smt->query();
        
        $result = $smt->fetchAll();
        
        $smt->closeCursor();
        
        
        if(!empty($result)) $result = $result[0];
        
        return $result;
    }
}

