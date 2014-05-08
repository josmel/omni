<?php

class Shop_Model_PayMethod extends Core_Model
{
    protected $_tablePayMethod; 
    
    public function __construct() {
        parent::__construct();
        $this->_tablePayMethod = new Application_Model_DbTable_PayMethod();
    }
    
    public function findPayMethodByCountryPairs($idCountry, $isLanding = false) {
        $smt = $this->_tablePayMethod->getAdapter()
                ->select()
                ->from(array('tp' => Application_Model_DbTable_PayMethod::NAMETABLE))
                ->join(array('ptp' => 'tpais_to_tipopago'), 
                        'tp.codtpag = ptp.codtpag', array('nombre'))
                ->where('tp.vchestado LIKE ?', 'A')
                ->where('ptp.codpais = ?', $idCountry);
        if ($isLanding) $smt->where('ptp.viewlanding = ?', '1');
        //echo $smt->assemble(); exit;
        $smt = $smt->query();
        
        $result = array('' => '-Seleccione un medio de pago');
        while ($row = $smt->fetch()) {
            $result["".$row['codtpag']] = $row['nombre'];
        }
        
        $smt->closeCursor();
        
        return $result;
    }
    
    public function getById($idPayMethod, $idCountry) {
        $smt = $this->_tablePayMethod->getAdapter()
                ->select()
                ->from(array('tp' => Application_Model_DbTable_PayMethod::NAMETABLE))
                ->join(array('ptp' => 'tpais_to_tipopago'), 
                        'tp.codtpag = ptp.codtpag', array('nombre', 'responseuri'))
                ->where('tp.vchestado LIKE ?', 'A')
                ->where('tp.codtpag LIKE ?', $idPayMethod)
                ->where('ptp.codpais = ?', $idCountry);
        
        //echo $smt->assemble(); exit;
        $smt = $smt->query();
        
        $result = $smt->fetchAll();
        
        $smt->closeCursor();
        
        if(!empty($result)) $result = $result[0];
        
        return $result;
    }
}

