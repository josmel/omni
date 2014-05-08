<?php

class Application_Model_DbTable_DocumentType extends Core_Db_Table {
    protected $_name = 'ttipodocumento';
    protected  $_primary = "idtdoc";
    const NAMETABLE='ttipodocumento';
    
    public function getPrimaryKey()
    {
        return $this->_primary;
    }
    
    public function getWhereActive()
    {
        return " ";
    }
}

