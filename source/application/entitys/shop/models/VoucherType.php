<?php

class Shop_Model_VoucherType extends Core_Model
{
    protected $_tableVoucherType; 
    
    public function __construct() {
        parent::__construct();
        $this->_tableVoucherType = new Application_Model_DbTable_VoucherType();
    }
    
    function findAll() {
        $result = array();
        
        $smt = $this->_tableVoucherType->select()
            ->where("vchestado = ?", "A")
            ->query();
        $result = $smt->fetchAll();
        $smt->closeCursor();
        
        return $result;
    }
    
    function findById($id) {
        $result = array();
        
        $smt = $this->_tableVoucherType->select()
            ->where("vchestado = ?", "A")
            ->where("codtdov = ?", $id)
            ->query();
        $result = $smt->fetchAll();
        
        if (!empty($result)) $result = $result[0];
        
        $smt->closeCursor();
        
        return $result;
    }
}

