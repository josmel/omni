<?php

class Businessman_Model_BusinessmanContact extends Core_Model
{
    protected $_table; 
    
    public function __construct() {
        $this->_table = new Application_Model_DbTable_BusinessmanContact();
    }
    
    public function updateFromChallenge($idBusinessman, $params) {
        $where = $this->_table->getAdapter()->quoteInto("vchestado LIKE 'A' AND codtcon LIKE 'TC5' AND codempr = ?", $idBusinessman);
        
        $data = array();
        if(isset($params['telefono'])) $data['telefono'] = $params['telefono'];
        if(isset($params['celular'])) $data['celular'] = $params['celular'];
        
        $smt = $this->_table->select()->where($where);
        $smt = $smt->query();
        $result = $smt->fetchAll();
        $smt->closeCursor();
        
        if (count($result) > 0) {
            $this->_table->update($data, $where);
        } else {
            $data['codempr'] = $idBusinessman;
            $data['codtcon'] = "TC5";
            $data['vchestado'] = 'A';
            $data['tmsfeccrea'] = date('Y-m-d H:i:s');
            $this->_table->insert($data);
            
        }
    }
}

