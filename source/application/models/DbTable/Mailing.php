<?php

class Application_Model_DbTable_Mailing extends Core_Db_Table
{
    protected $_name = 'tmailing';
    protected $_primary = "idmailing";
    const NAMETABLE='tmailing';
    
    static function populate($params)
    {
        $data = array();
        if(isset($params['iduser'])) $data['iduser'] = $params['iduser'];
        if(isset($params['name'])) $data['name'] = $params['name'];
        if(isset($params['apepat'])) $data['apepat'] = $params['apepat'];
        if(isset($params['apemat'])) $data['apemat'] = $params['apemat'];
        if(isset($params['email'])) $data['email'] = $params['email'];
        if(isset($params['login'])) $data['login'] = $params['login'];
        if(isset($params['password'])) $data['password'] = $params['password'];
        if(isset($params['lastlogin'])) $data['lastlogin'] = $params['lastlogin'];
        if(isset($params['roles'])) $data['roles'] = $params['roles'];
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
        return " ";
    }
}

