<?php

class Biller_Model_BuyDocument extends Core_Model
{
    protected $_table; 
    
    public function __construct() {
        $this->_table = new Application_Model_DbTable_BuyDocument();
    }
    
    public function findByBusinessman($idBusinessman, $beginDate = null, $endDate = null) {
        $smt = $this->_table->getAdapter()->select()
                ->from(
                    array('v' => Application_Model_DbTable_BuyDocument::NAMETABLE)
                )->join(array('d' => 'tdetventa'), 
                        "v.iddven = d.iddven AND d.vchestado LIKE 'A'",
                        array(
                            'cantidad' => 'COUNT(d.iddvnt)', 
                            'precio' => 'SUM(d.candpro * d.predpro)'
                            )
                        )
                ->where('v.vchestado LIKE ?', 'A')
                ->where('v.codempr = ?', $idBusinessman)
                ->group('v.iddven');
        
        if($beginDate != null) {
            $begin = $beginDate->get(Zend_Date::YEAR.'-'.Zend_Date::MONTH.'-'.Zend_Date::DAY);  
            $smt->where('v.fecdven >=  ?', $begin);
        }
        
        if($endDate != null) {
            $end = $endDate->get(Zend_Date::YEAR.'-'.Zend_Date::MONTH.'-'.Zend_Date::DAY);  
            $smt->where('v.fecdven <= ?', $end);
        }
        
        //echo $smt->assemble(); exit;
        $smt = $smt->query();

        
        $result =  $smt->fetchAll();
        $smt->closeCursor();
        
        return $result;
    }
    
    public function getById($id) {
        $smt = $this->_table->getAdapter()->select()
                ->from(
                    array('v' => Application_Model_DbTable_BuyDocument::NAMETABLE)
                )->join(array('d' => 'tdetventa'), 
                        "v.iddven = d.iddven AND d.vchestado LIKE 'A'",
                        array(
                            'cantidad' => 'COUNT(d.iddvnt)', 
                            'precio' => 'SUM(d.candpro * d.predpro)'
                            )
                        )
                ->where('v.vchestado LIKE ?', 'A')
                ->where('v.iddven = ?', $id)
                ->group('v.iddven');
        
        //echo $smt->assemble(); exit;
        $smt = $smt->query();

        
        $result =  $smt->fetchAll();
        $smt->closeCursor();
        
        return $result;
    }
    
    public function getDetails($idBuyDocument) {
        $smt = $this->_table->getAdapter()->select()
                ->from(
                    array('d' => 'tdetventa')
                )->join(array('p' => Application_Model_DbTable_Product::NAMETABLE), 
                        "p.codprod = d.codprod",
                        array('p.codprod', 'p.codtpro', 'p.desprod', 
                             'p.abrprod', 'p.shorttext', 'p.text', 
                             'p.punprod', 'p.slug', 'p.pesoprod', 
                             'p.desccaja', 'p.imgextdet', 'p.imgextcat')
                )
                ->where('d.vchestado LIKE ?', 'A')
                ->where('d.iddven = ?', $idBuyDocument);
        
        //echo $smt->assemble(); exit;
        $smt = $smt->query();

        
        $result =  $smt->fetchAll();
        $smt->closeCursor();
        
        return $result;
    }
    
    public function getPointsByBusinessman($idBusinessman) {
        $smt = $this->_table->getAdapter()->select()
                ->from(
                    array('v' => Application_Model_DbTable_BuyDocument::NAMETABLE), 
                    array('points' => 'SUM(v.pundven)')
                )->where('v.vchestado LIKE ?', 'A')
                ->where('v.estdven LIKE ?', 'FAC')
                ->where('v.codempr = ?', $idBusinessman)
                ->group('v.codempr');
        //echo $smt->assemble(); exit;
        $smt = $smt->query();

        
        $result =  $smt->fetch();
        $smt->closeCursor();
        
        $points = 0;
        if(!empty($result)) $points = $result['points'];
        
        return $points;
    }
}

