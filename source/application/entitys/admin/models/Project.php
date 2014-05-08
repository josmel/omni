<?php

class Admin_Model_Project extends Core_Model
{
    protected $_tableProject; 
    
    public function __construct() {
        $this->_tableProject = new Application_Model_DbTable_Project();
    }
    
    function getFetchPairsAllProjects() {
        $smt = $this->_tableProject->getAdapter()->select()
                ->from(
                        Application_Model_DbTable_Project::NAMETABLE, 
                        array('codproy', 'nombre')
                )->where("vchestado LIKE 'A'")->query();
        
        $result = array();
        while ($row = $smt->fetch()) {
            $result[$row['codproy']] = $row['nombre'];
        }
        $smt->closeCursor();
        
        return $result;
        
    }
}

