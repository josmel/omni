<?php

class Application_Model_DbTable_Week extends Core_Db_Table
{
    protected $_name = 'tsemana';
    protected $_primary = "codsema";
    const NAMETABLE='tsemana';
    
    public function getPrimaryKey()
    {
        return $this->_primary;
    }
    
    public function getWhereActive()
    {
        return " AND vchestado = 'A";
    }
}

