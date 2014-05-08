<?php

class Application_Model_DbTable_Quiz extends Core_Db_Table
{
    protected $_name = 'tencuesta';
    protected $_primary = "idencuesta";
    const NAMETABLE = 'tencuesta';
    
    public function __construct($config = array(), $definition = null) {
        parent::__construct($config, $definition);
        $this->setDefaultAdapter(Zend_Registry::get('dbAdmin'));
        $this->_setAdapter(Zend_Registry::get('dbAdmin'));
    }
    
    static function populate($params) {
        $data = array();
        if(isset($params['pregunta'])) $data['pregunta'] = $params['pregunta'];
        if(isset($params['encuestados'])) $data['encuestados'] = $params['encuestados'];
        if(isset($params['descripcion'])) $data['descripcion'] = $params['descripcion'];
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
      //  
        return array("pregunta","encuestados","tmsfeccrea", "IF(vchestado LIKE 'A', 'Activo', 'Inactivo')");
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
            $where = $this->getDefaultAdapter()->quoteInto('idencuesta LIKE ?', $data['idencuesta']);
            $this->update(array('vchestado' => 'I'), $where);
        } 
        
        parent::insert($data);
    }
    
//    public function update(array $data, $where) {
//
//        if($data['vchestado'] == 'A' && isset($data['idencuesta'])) {
//            //Deshabilitar resto
//            $whereActive = $this->getDefaultAdapter()->quoteInto('idencuesta LIKE ?', $data['idencuesta']);
//            $this->update(array('vchestado' => 'I'), $whereActive);
//        } 
//        parent::update($data, $where);
//    }
}

