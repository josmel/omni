<?php

class Application_Model_DbTable_StreetType extends Core_Db_Table
{

    protected $_name = 'ttipvia';
    protected  $_primary = "codtvia";
    const NAMETABLE='ttipvia';
    
    public function getPrimaryKey()
    {
        return $this->_primary;
    }
    
    public function getWhereActive()
    {
        return " ";
    }
}

