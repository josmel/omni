<?php

class Businessman_Model_Report extends Core_Model {

    protected $_tableCountry;
    protected $_tableWeek;
    protected $_tableAffliate;

    public function __construct() {
        $this->_tableCountry = new Application_Model_DbTable_Country();
        $this->_tableWeek = new Application_Model_DbTable_Week();
         $this->_tableAffliate = new Application_Model_DbTable_Affiliate();
    }

    public function getCountryAll() {
        $smt = $this->_tableCountry->getAdapter()->select()
                        ->from($this->_tableCountry->getName())
                        ->where("vchestado LIKE 'A'")->query(); 

        $result = array();
        while ($row = $smt->fetch()) {
            $result[$row['codpais']] = $row['nompais'];
        }
        $smt->closeCursor();
        return $result;
    }
   
      public function getAffiliate($id) {
        $smt = $this->_tableAffliate->getAdapter()->select()
                        ->from($this->_tableAffliate->getName())
                        ->where("vchestado LIKE 'A'")
                ->where('codempr= ?',$id)
                ->query(); 
         $result = $smt->fetch();
        $smt->closeCursor();
        return $result;

        
    }
    
      public function getWeekAll($idafil) {
        $smt = $this->_tableWeek->getAdapter()->select()
                        ->from($this->_tableWeek->getName())
                         ->where("vchestado LIKE 'A'")
                         ->where('codsema >= ?', $idafil)
                ->query(); 
        $result = array();
        while ($row = $smt->fetch()) {
            $result[$row['codsema']] = $row['dessema'];
        }
        $smt->closeCursor();
        return $result;
    }
    
     public function getWeekOne($idafil) {
        $smt = $this->_tableWeek->getAdapter()->select()
                        ->from($this->_tableWeek->getName())
                         ->where("vchestado LIKE 'A'")
                         ->where('codsema >= ?', $idafil)
                ->order('codsema desc')
                    ->query(); 
         $result = $smt->fetch();
        $smt->closeCursor();
        return $result;
    }
    
}

