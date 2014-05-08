<?php

class Shop_Model_Order extends Core_Model
{
    protected $_tableOrder; 
    
    public function __construct() {
        $this->_tableOrder = new Application_Model_DbTable_Order();
    }
    
    public function findById($id) {
        $where = $this->_tableOrder->getAdapter()->quoteInto('vchestado LIKE ?', 'A');
        $where .= " ".$this->_tableOrder->getAdapter()->quoteInto('AND idpedi = ?', $id);
        
        $result = $this->_tableOrder->fetchAll($where);
        
        if(count($result) > 0) $result = $result[0];
        
        return $result;
    }
    
    public function findAllByJoined($idJoined) {
        $smt = $this->_tableOrder->getAdapter()->select()
                ->from(
                    array('o' => Application_Model_DbTable_Order::NAMETABLE), 
                    array('o.idpedi', 'o.estpedi',
                        'fecpedi' => "DATE_FORMAT(o.fecpedi, '%d/%m/%Y')")
                )->join(array('u' => Application_Model_DbTable_Ubigeo::NAMETABLE), 
                        'o.codubig = u.codubig AND u.codpais = o.codpais',
                        array('ivaubig' => 'u.ivaubig'))
                ->join(array('d' => 'tdetpedido'), 
                        "o.idpedi = d.idpedi AND d.vchestado LIKE 'A'",
                        array(
                            'cantidad' => 'COUNT(d.idpedi)', 
                            'precio' => 'SUM(d.candpro * d.predpro)'
                            )
                        )
                ->where('o.vchestado LIKE ?', 'A')
                ->where('o.idcliper = ?', $idJoined)
                ->group('o.idpedi');
                
        //echo $smt->assemble(); exit;
        $smt = $smt->query();
        
        $result = $smt->fetchAll();
        
        $smt->closeCursor();
        
        return $result;
    }
    
    public function findPendantsByBusinessman($idBusinessman) {
        $smt = $this->_tableOrder->getAdapter()->select()
                ->from(
                    array('o' => Application_Model_DbTable_Order::NAMETABLE), 
                    array('o.idpedi', 'o.estpedi',
                        'fecpedi' => "DATE_FORMAT(o.fecpedi, '%d/%m/%Y')",
                        'horpedi' => "DATE_FORMAT(o.fecpedi, '%H:%i')")
                )->joinLeft(array('u' => Application_Model_DbTable_Ubigeo::NAMETABLE), 
                        'o.codubig = u.codubig AND u.codpais = o.codpais',
                        array('ivaubig' => 'u.ivaubig'))
                ->join(array('de' => Application_Model_DbTable_ShipAddress::NAMETABLE), 
                        'de.idpedi = o.idpedi',
                        array('de.ubidenv'))
                ->join(array('pag' => 'tmediopago'), 
                        'pag.idpedi = o.idpedi AND u.codpais = o.codpais',
                        array('pag.codtpag'))
                ->join(array('ptp' => 'tpais_to_tipopago'), 
                        'ptp.codtpag = pag.codtpag AND ptp.codpais = o.codpais',
                        array('mediopago' => 'ptp.nombre'))
                ->join(array('d' => 'tdetpedido'), 
                        "o.idpedi = d.idpedi AND d.vchestado LIKE 'A'",
                        array(
                            'cantidad' => 'COUNT(d.idpedi)', 
                            'precio' => 'SUM(d.candpro * d.predpro)'
                            )
                )->where('o.vchestado LIKE ?', 'A')
                ->where('o.idcliper IS NULL')
                ->where('o.codempr = ?', $idBusinessman)
                ->where('o.estpedi LIKE ?', 'PEN')
                ->group('o.idpedi');
                
        //echo $smt->assemble(); exit;
        $smt = $smt->query();
        
        $result = $smt->fetchAll();
        
        $smt->closeCursor();
        
        return $result;
    }
    public function inactiveOrder($id)
    {
        $succesTransaq=true;
        try{
            $where = $this->_tableOrder->getAdapter()->quoteInto('idpedi = ?',$id);  
            $data = array('vchestado' =>'D');
            $this->_tableOrder->update($data, $where);
        }  catch (Exception $e){
            $succesTransaq=$e->getMessage();
        }      
    }
}

