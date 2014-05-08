<?php

class Application_Model_DbTable_ShipPrice extends Core_Db_Table
{

    protected $_name = 'tflete';
    protected  $_primary = "idflet";
    const NAMETABLE='tflete';
    
    public function getPrimaryKey()
    {
        return $this->_primary;
    }
    
    public function getWhereActive()
    {
        return " ";
    }
}

