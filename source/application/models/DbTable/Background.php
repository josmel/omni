<?php

class Application_Model_DbTable_Background extends Core_Db_Table
{
    protected $_name = 'tfondo';
    protected $_primary = "idfondo";
    const NAMETABLE='tfondo';
    
    public function __construct($config = array(), $definition = null) {
        parent::__construct($config, $definition);
        //$this->setDefaultAdapter(Zend_Registry::get('dbAdmin'));
        $this->_setAdapter(Zend_Registry::get('dbAdmin'));
    }
    
    static function populate($params) {
        $data = array();
        if(isset($params['codtfondo'])) $data['codtfondo'] = $params['codtfondo'];
        if(isset($params['titulo'])) $data['titulo'] = $params['titulo'];
        if(isset($params['idimagen'])) $data['idimagen'] = $params['idimagen'];
        if(isset($params['vchusucrea'])) $data['vchusucrea'] = $params['vchusucrea'];
        if(isset($params['tmsfeccrea'])) $data['tmsfeccrea'] = $params['tmsfeccrea'];
        if(isset($params['vchusumodif'])) $data['vchusumodif'] = $params['vchusumodif'];
        if(isset($params['tmsfecmodif'])) $data['tmsfecmodif'] = $params['tmsfecmodif'];
        //$data = array_filter($data);
        if(isset($params['vchestado']))
            $data['vchestado'] = $params['vchestado'] == '1' ? 'A' 
                : ($params['vchestado'] != 'D' ? 'I' : 'D');
        //var_dump($data); exit;
        return $data;
    }
    
    /**
     * 
     * @param obj DB $resulQuery
     */
    public function columnDisplay()
    {
        return array("titulo", "(SELECT nombre from ttipofondo where codtfondo = tfondo.codtfondo)", "IF(vchestado LIKE 'A', 'Activo', 'Inactivo')");
    }
        
    public function getPrimaryKey() {
        return $this->_primary;
    }
    
    public function getWhereActive() {
        return " AND NOT vchestado LIKE 'D'";
    }
    
    public function insert(array $data) {
        if($data['vchestado'] == 'A') {
            //Deshabilitar resto
            $where = $this->getDefaultAdapter()->quoteInto('codtfondo LIKE ?', $data['codtfondo']);
            $this->update(array('vchestado' => 'I'), $where);
        } 
        
        parent::insert($data);
    }
    
    public function update(array $data, $where) {
        if($data['vchestado'] == 'A' && isset($data['codtfondo'])) {
            //Deshabilitar resto
            $whereActive = $this->getDefaultAdapter()->quoteInto('codtfondo LIKE ?', $data['codtfondo']);
            $this->update(array('vchestado' => 'I'), $whereActive);
        } 
        parent::update($data, $where);
    }
}

