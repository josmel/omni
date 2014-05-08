<?php

class Application_Model_DbTable_Product extends Core_Db_Table
{

    protected $_name = 'tproducto';
    protected  $_primary = "codprod";
    const NAMETABLE='tproducto';
    
    static function populate($params) {
        $data = array();
        if(isset($params['codprod'])) $data['codprod'] = $params['codprod'];
        if(isset($params['desprod'])) $data['desprod'] = $params['desprod'];
        if(isset($params['abrprod'])) $data['abrprod'] = $params['abrprod'];
        if(isset($params['shorttext'])) $data['shorttext'] = $params['shorttext'];
        if(isset($params['text'])) $data['text'] = $params['text'];
        if(isset($params['issalient'])) $data['issalient'] = $params['issalient'];
        if(isset($params['imgextcat'])) $data['imgextcat'] = $params['imgextcat'];
        if(isset($params['imgextdet'])) $data['imgextdet'] = $params['imgextdet'];

        return $data;
    }
    
    /**
     * 
     * @param obj DB $resulQuery
     */
    public function columnDisplay()
    {
        return array("codprod", "desprod", "IF(vchestado LIKE 'A', 'Activo', 'Inactivo')");
    }
        
    public function getPrimaryKey() {
        return $this->_primary;
    }
    
    public function getWhereActive() {
        return " AND vchestado LIKE 'A'=";
    }
}

