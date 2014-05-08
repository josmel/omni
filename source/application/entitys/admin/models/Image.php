<?php

class Admin_Model_Image extends Core_Model
{
    protected $_tableImage; 
    
    public function __construct() {
        $this->_tableImage = new Application_Model_DbTable_Image();
    }    
    
    public function insert($params) {
        $data = array();
          
        if(isset($params['nombre'])) $data['nombre'] = $params['nombre'];
        if(isset($params['vchestado'])) 
            $data['vchestado'] = $params['vchestado'] == 1 ? 'A' : 'I';
        if(isset($params['tmsfeccrea'])) date('Y-m-d H:i:s');
        if(isset($params['vchusucrea'])) $data['vchusucrea'] = $params['vchusucrea'];
        
        $this->_tableImage->insert($data);
        return $this->_tableImage->getAdapter()->lastInsertId();
    }
    
}
