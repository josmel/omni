<?php

class Shop_Model_ShipPrice extends Core_Model
{
    protected $_tableShipPrice; 
    
    public function __construct() {
        parent::__construct();
        $this->_tableShipPrice = new Application_Model_DbTable_ShipPrice();
    }
    
    protected function _getByUbigeo($idUbigeo, $idCountry) {
         $smt = $this->_tableShipPrice->getAdapter()
                ->select()
                ->from(array('sp' => Application_Model_DbTable_ShipPrice::NAMETABLE))
                ->where('sp.vchestado LIKE ?', 'A')
                ->where('sp.codpais = ?', $idCountry)
                ->where('sp.codubig LIKE ?', $idUbigeo);
        
        //echo $smt->assemble(); exit;
        $smt = $smt->query();
        
        $result = $smt->fetchAll();
        
        $smt->closeCursor();
        
        if(!empty($result)) $result = $result[0];
        
        return $result;
    }
    
    public function getByUbigeo($idUbigeo, $idCountry) {
        $result = $this->_getByUbigeo($idUbigeo, $idCountry);
        
        if(empty($result) && substr($idUbigeo, 6, 4) != '0000') {
            $idUbigeo = substr($idUbigeo, 0, 6).'0000';
            $result = $this->_getByUbigeo($idUbigeo, $idCountry);
        }
        
        if(empty($result) && substr($idUbigeo, 2, 8) != '00000000') {
            $idUbigeo = substr($idUbigeo, 0, 2).'00000000';
            $result = $this->_getByUbigeo($idUbigeo, $idCountry);
        } 
        
        return $result;
    }
}

