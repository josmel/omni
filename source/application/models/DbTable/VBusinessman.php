<?php

class Application_Model_DbTable_VBusinessman extends Core_Db_Table
{
    protected $_name = 'vbusinessman';
    protected $_primary = "codempr";
    const NAMETABLE = 'vbusinessman';
    
    /**
     * 
     * @param obj DB $resulQuery
     */
        
    public function getPrimaryKey() {
        return $this->_primary;
    }
    
    public function getWhereActive() {
        return " ";
    }
    
    public function getBySubDomain($subDomain) {
        $smt = $this->select()
                ->from(array('e' => $this->_name))
                ->where('e.subdomain LIKE ?', $subDomain)->query();
       
        $result = $smt->fetchAll();
        $smt->closeCursor();
        
        if(!empty($result)) { 
            $result = $result[0];
        }
        return $result;
    }
    
    public function getById($id) {
        $smt = $this->select()
                ->from(array('e' => $this->_name))
                ->where('e.codempr LIKE ?', $id)->query();
       
        $result = $smt->fetchAll();
        $smt->closeCursor();
        
        if(!empty($result)) { 
            $result = $result[0];
        }
        return $result;
    }
}

