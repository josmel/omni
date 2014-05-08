<?php

class Application_Model_DbTable_BuyDocument extends Core_Db_Table
{
    protected $_name = 'tdocventa';
    protected $_primary = "iddven";
    const NAMETABLE='tdocventa';
    
    public function getPrimaryKey()
    {
        return $this->_primary;
    }
    
    public function getWhereActive()
    {
        return " AND vchestado = 'A";
    }
}

