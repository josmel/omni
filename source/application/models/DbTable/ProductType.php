<?php

class Application_Model_DbTable_ProductType extends Core_Db_Table
{

    protected $_name = 'ttipproducto';
    protected  $_primary = "codtpro";
    const NAMETABLE='ttipproducto';
    
     static function populate($params) {
        $data = array();
        if(isset($params['codtprod'])) $data['codtprod'] = $params['codtprod'];      
        if(isset($params['destprod'])) $data['destprod'] = $params['destprod'];
        if(isset($params['abrtprod'])) $data['abrtprod'] = $params['abrtprod'];        
               
        return $data;
    }
    
    public function getPrimaryKey()
    {
        return $this->_primary;
    }
    
    public function getWhereActive()
    {
        return "";
    }
    
    public function columnDisplay()
    {
        return array("codtpro", "destprod", 
            "IF(vchestado LIKE 'A', 'Activo', 'Inactivo')",
            "IF(flagview LIKE 1, 'Sistemas', '')");
    }
}

