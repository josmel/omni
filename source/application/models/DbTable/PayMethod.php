<?php

class Application_Model_DbTable_PayMethod extends Core_Db_Table
{

    protected $_name = 'ttipopago';
    protected  $_primary = "codtpag";
    const NAMETABLE='ttipopago';
    
    public function getPrimaryKey()
    {
        return $this->_primary;
    }
    
    public function getWhereActive()
    {
        return " ";
    }
}

