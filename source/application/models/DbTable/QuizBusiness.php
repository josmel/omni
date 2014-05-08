<?php

class Application_Model_DbTable_QuizBusiness extends Core_Db_Table
{
    protected $_name = 'tempresario_encuesta';
    protected $_primary = "idencuesta";
    const NAMETABLE = 'tempresario_encuesta';
    
    public function __construct($config = array(), $definition = null) {
        parent::__construct($config, $definition);
        $this->setDefaultAdapter(Zend_Registry::get('dbAdmin'));
        $this->_setAdapter(Zend_Registry::get('dbAdmin'));
    }
    
    /**
     * 
     * @param obj DB $resulQuery
     */

}

