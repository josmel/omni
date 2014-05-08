<?php

class Application_Model_DbTable_Affiliate extends Core_Db_Table {

    protected $_name = 'tafiliacion';
    protected $_primary = "idafil";

    const NAMETABLE = 'tafiliacion';

    public function getPrimaryKey() {
        return $this->_primary;
    }

    public function getWhereActive() {
        return " AND vchestado = 'A";
    }

}

