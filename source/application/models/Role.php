<?php

class Application_Model_Role extends Core_Model
{
    protected $_tableRoel; 
    
    public function __construct() {
        $this->_tableRole = new Application_Model_DbTable_Role();
    }
    
    public function getRolesByUser($idUser) {
        $roles = $this->_tableRole->getByUser($idUser);
        
        return $this->fetchPairs($roles);
    }
    
    function getAllRoles() {
        return $this->_tableRole->getAll('state = 1');
    }
    
    function getFetchPairsAllRoles() {
        return $this->fetchPairs($this->getAllRoles());
    }
}

