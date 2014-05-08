<?php

class Application_Model_DbTable_BusinessmanContact extends Core_Db_Table
{
    protected $_name = 'tcontacto';
    protected $_primary = "idcont";
    const NAMETABLE='tcontacto';
    
    public function getPrimaryKey()
    {
        return $this->_primary;
    }
    
    public function getWhereActive()
    {
        return " AND vchestado = 'A";
    }
}

