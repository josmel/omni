<?php

class Application_Model_DbTable_OrderDataTemp extends Core_Db_Table
{

    protected $_name = 'torderdatatemp';
    protected  $_primary = "id";
    const NAMETABLE='torderdatatemp';
    
    public function getPrimaryKey()
    {
        return $this->_primary;
    }
    
    public function getWhereActive()
    {
        return " ";
    }
}

