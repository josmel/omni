<?php

class Businessman_Model_DocumentType extends Core_Model
{
    protected $_tableDocumentType; 
    
    public function __construct() {
        parent::__construct();
        $this->_tableDocumentType = new Application_Model_DbTable_DocumentType();
    }
    
    function getAllPairs() {
        $smt = $this->_tableDocumentType->select()
            ->where("vchestado = ?", "A")
            ->query();
        
        $result = array();
        
        while ($row = $smt->fetch()) {
            $result[$row['idtdoc']] = $row['descdoc'];
        }
        
        $smt->closeCursor();
        return $result;
        
    }
    
    public function findById($id) {
        $where = $this->_tableDocumentType->getAdapter()->quoteInto('vchestado LIKE ?', 'A');
        $where .= " ".$this->_tableDocumentType->getAdapter()->quoteInto('AND idtdoc = ?', $id);
        
        $result = $this->_tableDocumentType->fetchAll($where);
        
        if(!empty($result)) $result = $result[0];
        
        return $result;
    }
}

