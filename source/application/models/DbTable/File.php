<?php

class Application_Model_DbTable_File extends Core_Db_Table
{
    protected $_name = 'tfile';
    protected $_primary = "idfile";
    const NAMETABLE = 'tfile';
    
    public function __construct($config = array(), $definition = null) {
        parent::__construct($config, $definition);
        //$this->setDefaultAdapter(Zend_Registry::get('dbAdmin'));
        $this->_setAdapter(Zend_Registry::get('dbAdmin'));
    }
    
    static function populate($params) {
        $data = array();
        if(isset($params['codtfile'])) $data['codtfile'] = $params['codtfile'];
        if(isset($params['nombre'])) $data['nombre'] = $params['nombre'];
        if(isset($params['descripcion'])) $data['descripcion'] = $params['descripcion'];
        if(isset($params['extfile'])) $data['extfile'] = $params['extfile'];
        if(isset($params['titulo'])) $data['titulo'] = $params['titulo'];
        if(isset($params['vchusucrea'])) $data['vchusucrea'] = $params['vchusucrea'];
        if(isset($params['tmsfeccrea'])) $data['tmsfeccrea'] = $params['tmsfeccrea'];
        if(isset($params['vchusumodif'])) $data['vchusumodif'] = $params['vchusumodif'];
        if(isset($params['tmsfecmodif'])) $data['tmsfecmodif'] = $params['tmsfecmodif'];
        //$data = array_filter($data);
        if(isset($params['vchestado']))
            $data['vchestado'] = $params['vchestado'] == '1' ? 'A' 
                : ($params['vchestado'] != 'D' ? 'I' : 'D');
        return $data;
    }
    
    /**
     * 
     * @param obj DB $resulQuery
     */
    public function columnDisplay()
    {
        return array("titulo", "(SELECT nombre from ttipofile where codtfile = tfile.codtfile)", "IF(vchestado LIKE 'A', 'Activo', 'Inactivo')");
    }
        
    public function getPrimaryKey() {
        return $this->_primary;
    }
    
    public function getWhereActive() {
        return " AND NOT vchestado LIKE 'D'";
    }
}

