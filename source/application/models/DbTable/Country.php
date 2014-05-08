<?php

class Application_Model_DbTable_Country extends Core_Db_Table {

    protected $_name = 'tpais';
    protected $_primary = "codpais";

    const NAMETABLE = 'tpais';

    public function getPrimaryKey() {
        return $this->_primary;
    }

    public function getWhereActive() {
        return " AND vchestado = 'A";
    }

}

