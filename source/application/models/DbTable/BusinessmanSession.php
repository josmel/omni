<?php

class Application_Model_DbTable_BusinessmanSession extends Core_Db_Table {
    protected $_name = 'tsession';
    protected  $_primary = "id";
    const NAMETABLE='tsession';
    
    public function __construct($config = array(), $definition = null) {
        parent::__construct($config, $definition);
        //$this->setDefaultAdapter(Zend_Registry::get('dbAdmin'));
        $this->_setAdapter(Zend_Registry::get('dbOV'));
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

