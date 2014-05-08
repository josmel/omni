<?php

class Application_Model_DbTable_ShipAddress extends Core_Db_Table
{
    protected $_name = 'tdirenvio';
    protected $_primary = "iddenv";
    const NAMETABLE='tdirenvio';
    
    static function populate($params)
    {
        $data = array();
        if(isset($params['iduser'])) $data['iduser'] = $params['iduser'];
        if(isset($params['lastpasschange'])) $data['lastpasschange'] = $params['lastpasschange'];
        $data=  array_filter($data);
        $data['state']=isset($params['state'])?$params['state']:1;
        return $data;
    }

    /**
     * 
     * @param obj DB $resulQuery
     */
    public function columnDisplay()
    {
        return array("CONCAT(name, ' ', apepat, ' ', apemat)",'email',"IF(state=1, 'Activo', 'Inactivo')");
    }
        
    public function getPrimaryKey()
    {
        return $this->_primary;
    }
    
    public function getWhereActive()
    {
        return " AND vchestado = 'A";
    }
}

