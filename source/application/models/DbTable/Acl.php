<?php

class Application_Model_DbTable_Acl extends Core_Db_Table
{
    protected $_name = 'tacl';
    protected  $_primary = "idacl";
  
    public function __construct($config = array(), $definition = null) {
        parent::__construct($config, $definition);
        //$this->setDefaultAdapter(Zend_Registry::get('dbAdmin'));
        $this->_setAdapter(Zend_Registry::get('dbAdmin'));
    }
    
    public function getWhereActive() {
        return " AND state=1 ";
    }
    
    public function getByRole($idRole) {
        $smt = $this->select()->from(array('a' => $this->_name))->join(array('ar' => 'tacl_to_roles'), 'a.idacl = ar.idacl', array())
                ->where('ar.idrol = ?',$idRole)
                ->query();
        $result = $smt->fetchAll();
        $smt->closeCursor();
        return $result;
    }
    
}

