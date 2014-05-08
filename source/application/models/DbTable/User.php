<?php

class Application_Model_DbTable_User extends Core_Db_Table
{
    protected $_name = 'tusers';
    protected $_nameUserRoles = 'tusers_to_roles';
    protected $_primary = "iduser";
    const NAMETABLE='tusers';
    
    public function __construct($config = array(), $definition = null) {
        parent::__construct($config, $definition);
        //$this->setDefaultAdapter(Zend_Registry::get('dbAdmin'));
        $this->_setAdapter(Zend_Registry::get('dbAdmin'));
    }
    
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

    public function insert(array $data) {
        $roles = $data['roles'];
        unset($data['roles']);
        parent::insert($data);
        
        $idUser = $this->_db->lastInsertId();
        $this->addRoles($idUser, $roles);     
    }
    
    public function update(array $data, $where) {
        $roles = array();
        if(isset($data['roles'])) {
            $roles = $data['roles'];
            unset($data['roles']);       
            $idUser = $data[$this->_primary];
            $this->addRoles($idUser, $roles); 
        }
        parent::update($data, $where);
                
    }
    
    private function addRoles($idUser, $roles) {
        $this->_db->delete($this->_nameUserRoles, $this->_db->quoteInto('iduser = ?', $idUser));
        foreach($roles as $idRol) {
            $this->_db->insert($this->_nameUserRoles, array('idrol' => $idRol, 'iduser' => $idUser));
        }
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
        return " AND state= 1";
    }
}

