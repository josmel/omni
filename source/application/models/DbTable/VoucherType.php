<?php

class Application_Model_DbTable_VoucherType extends Core_Db_Table
{
    protected $_name = 'ttipdocventa';
    protected $_primary = "codtdov";
    const NAMETABLE='ttipdocventa';
    
    static function populate($params) {
        $data = array();
        if(isset($params['vchusucrea'])) $data['vchusucrea'] = $params['vchusucrea'];
        if(isset($params['tmsfeccrea'])) $data['tmsfeccrea'] = $params['tmsfeccrea'];

        //$data = array_filter($data);
        if(isset($params['vchestado']))
            $data['vchestado']= $params['vchestado'] ? 'A' : 'I';
        return $data;
    }
    
    public function getPrimaryKey() {
        return $this->_primary;
    }
}

