<?php

class Businessman_Model_Ubigeo extends Core_Model
{
    protected $_tableUbigeo; 
    
    public function __construct() {
        $this->_tableUbigeo = new Application_Model_DbTable_Ubigeo();
    }
    
    public function findAllByCountryPairs($idCountry, $parent = "", $default = null) {
        
        $smt = $this->_tableUbigeo->select()
                ->where('vchestado LIKE ?', 'A')
                ->where('NOT codubig = ?', '0000000000')
                ->where('codpais = ?', $idCountry);

        if(!empty($parent)) 
            $smt = $smt->where('codupar LIKE ?', $parent);
        else
            $smt = $smt->where('codupar IS NULL');
       
        $smt = $smt->order('desubig ASC');
        //echo $smt->assemble(); exit;
        $smt = $smt->query();

        $result = array();
        if ($default != null) $result[""] = $default;
        
        while ($row = $smt->fetch()) {
            $result[$row['codubig']] = $row['desubig'];
        }
        $smt->closeCursor();
        
        return $result;
    }
    
    public function findById($idCountry, $id) {
        $where = $this->_tableUbigeo->getAdapter()->quoteInto('vchestado LIKE ?', 'A');
        $where .= " ".$this->_tableUbigeo->getAdapter()->quoteInto('AND codubig = ?', $id);
        $where .= " ".$this->_tableUbigeo->getAdapter()->quoteInto('AND codpais = ?', $idCountry);
        
        $result = $this->_tableUbigeo->fetchAll($where);
        
        if(count($result) > 0) $result = $result[0];
        
        return $result;
    }
}

