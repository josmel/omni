<?php

class Admin_Model_Rol extends Core_Model
{
    protected $_tableUser; 
    
    public function __construct() {
        $this->_tableUser = new Application_Model_DbTable_User();
    }
    
    function getAllUsers() {
        return $this->_tableUser->getAll();
    }
}

