<?php

class Application_Model_DbTable_Discount extends Core_Db_Table
{

    protected $_name = 'tdescuento';
    protected  $_primary = 'iddesc';
    const NAMETABLE = 'tdescuento';
    
    public function getPrimaryKey()
    {
        return $this->_primary;
    }
    
    public function getWhereActive()
    {
        return " ";
    }
}

