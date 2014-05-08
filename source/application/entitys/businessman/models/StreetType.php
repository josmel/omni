<?php

class Businessman_Model_StreetType extends Core_Model
{
    protected $_tableStreetType; 
    
    public function __construct() {
        parent::__construct();
        $this->_tableStreetType = new Application_Model_DbTable_StreetType();
    }
    
    function getAllPairs($descColum = 'destvia') {
        $smt = $this->_tableStreetType->select()
            ->where("vchestado = ?", "A")
            ->query();
        
        $result = array();
        
        while ($row = $smt->fetch()) {
            $result[$row['codtvia']] = $row[$descColum];
        }
        
        $smt->closeCursor();
        return $result;
        
    }
    
    public function findById($id) {
        $where = $this->_tableStreetType->getAdapter()->quoteInto('vchestado LIKE ?', 'A');
        $where .= " ".$this->_tableStreetType->getAdapter()->quoteInto('AND codtvia = ?', $id);
        
        $result = $this->_tableStreetType->fetchAll($where);
        
        if(!empty($result)) $result = $result[0];
        
        return $result;
    }
}

