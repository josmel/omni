<?php

class Application_Model_DbTable_QuizType extends Core_Db_Table
{
    protected $_name = 'tencuesta_alter';
    protected $_primary = "idtencuestaalr";
    const NAMETABLE = 'tencuesta_alter';
    
    public function __construct($config = array(), $definition = null) {
        parent::__construct($config, $definition);
        $this->setDefaultAdapter(Zend_Registry::get('dbAdmin'));
        $this->_setAdapter(Zend_Registry::get('dbAdmin'));
    }
    
    static function populate($params) {
        $data = array();
        if(isset($params['alternativa'])) $data['alternativa'] = $params['alternativa'];
        if(isset($params['tencuesta'])) $data['tencuesta'] = $params['tencuesta'];
        return $data;
    }
    
    /**
     * 
     * @param obj DB $resulQuery
     */
    public function columnDisplay()
    {
        return array("pregunta", "IF(vchestado LIKE 'A', 'Activo', 'Inactivo')");
    }
    
    public function getPrimaryKey() {
        return $this->_primary;
    }

}

