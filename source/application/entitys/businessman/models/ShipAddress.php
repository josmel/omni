<?php

class Businessman_Model_ShipAddress extends Core_Model
{
    protected $_tableAddress; 
    
    public function __construct() {
        $this->_tableAddress = new Application_Model_DbTable_ShipAddress();
    }
    
    
    public function insert($params) {
        $data = array();
        if(isset($params['idcliper'])) $data['idcliper'] = $params['idcliper'];
        if(isset($params['codempr'])) $data['codempr'] = $params['codempr'];
        if(isset($params['codtvia'])) $data['codtvia'] = $params['codtvia'];
        if(isset($params['ubidenv'])) $data['ubidenv'] = $params['ubidenv'];
        if(isset($params['codubig'])) $data['codubig'] = $params['codubig'];
        if(isset($params['codpais'])) $data['codpais'] = $params['codpais'];
        if(isset($params['tvidire'])) $data['tvidire'] = $params['tvidire'];
        if(isset($params['numdire'])) $data['numdire'] = $params['numdire'];
        if(isset($params['intdire'])) $data['intdire'] = $params['intdire'];
        if(isset($params['refdenvio'])) $data['refdenvio'] = $params['refdenvio'];
        if(isset($params['vchestado'])) $data['vchestado'] = $params['vchestado'];
        if(isset($params['vchusumodif'])) $data['vchusumodif'] = $params['vchusumodif'];
        if(isset($params['vchequipo'])) $data['vchequipo'] = $params['vchequipo'];
        if(isset($params['vchprograma'])) $data['vchprograma'] = $params['vchprograma'];
        
        $this->_tableAddress->insert($data);
        return $this->_tableAddress->getAdapter()->lastInsertId();
    }
    
    public function update($idAddress, $params) {
        $where = $this->_tableAddress->getAdapter()->quoteInto('iddenv = ?', $idAddress);
        
        $data = array();
        if(isset($params['idcliper'])) $data['idcliper'] = $params['idcliper'];
        if(isset($params['codempr'])) $data['codempr'] = $params['codempr'];
        if(isset($params['codtvia'])) $data['codtvia'] = $params['codtvia'];
        if(isset($params['ubidenv'])) $data['ubidenv'] = $params['ubidenv'];
        if(isset($params['codubig'])) $data['codubig'] = $params['codubig'];
        if(isset($params['codpais'])) $data['codpais'] = $params['codpais'];
        if(isset($params['tvidire'])) $data['tvidire'] = $params['tvidire'];
        if(isset($params['numdire'])) $data['numdire'] = $params['numdire'];
        if(isset($params['intdire'])) $data['intdire'] = $params['intdire'];
        if(isset($params['refdenvio'])) $data['refdenvio'] = $params['refdenvio'];
        if(isset($params['vchestado'])) $data['vchestado'] = $params['vchestado'];
        if(isset($params['vchusumodif'])) $data['vchusumodif'] = $params['vchusumodif'];
        if(isset($params['vchequipo'])) $data['vchequipo'] = $params['vchequipo'];
        if(isset($params['vchprograma'])) $data['vchprograma'] = $params['vchprograma'];
        $data['tmsfecmodif'] = date('Y-m-d H:i:s');
        
        $this->_tableAddress->update($data, $where);
    }

    public function findById($id) {
        $where = $this->_tableAddress->getAdapter()->quoteInto('vchestado LIKE ?', 'A');
        $where .= " ".$this->_tableAddress->getAdapter()->quoteInto('AND iddenv = ?', $id);
        
        $result = $this->_tableAddress->fetchAll($where);
        
        if(!empty($result)) $result = $result[0];
        
        return $result;
    }
    
    public function findAllByJoined($idJoined) {
        $where = $this->_tableAddress->getAdapter()->quoteInto('vchestado LIKE ?', 'A');
        $where .= " ".$this->_tableAddress->getAdapter()->quoteInto('AND idcliper = ?', $idJoined);
        
        $result = $this->_tableAddress->fetchAll($where);
        
        return $result;
    }
    
    public function findAllByBusinessMan($idBusinessman) {
        $where = $this->_tableAddress->getAdapter()->quoteInto('vchestado LIKE ?', 'A');
        $where .= " ".$this->_tableAddress->getAdapter()->quoteInto('AND  = ?', $idJoined);
        
        $result = $this->_tableAddress->fetchAll($where);
        
        return $result;
    }
    
    
    public function findAllByJoinedPairs($idJoined, $idCountry) {
        $where = $this->_tableAddress->getAdapter()->quoteInto('codpais = ?', $idCountry);
        
        $smt = $this->_tableAddress->getAdapter()
                ->select()
                ->from(array('a' => Application_Model_DbTable_ShipAddress::NAMETABLE))
                ->join(array('u1' => Application_Model_DbTable_Ubigeo::NAMETABLE), 
                        'a.codubig = u1.codubig AND u1.'.$where,
                        array('codstate' => 'u1.codubig', 'namedistrict' => 'u1.desubig'))
                ->join(array('u2' => Application_Model_DbTable_Ubigeo::NAMETABLE), 
                        'u1.codupar = u2.codubig AND u2.'.$where,
                        array('codprovince' => 'u2.codubig', 'nameprovince' => 'u2.desubig'))
                ->join(array('u3' => Application_Model_DbTable_Ubigeo::NAMETABLE), 
                        'u2.codupar = u3.codubig AND u3.'.$where,
                        array('coddistrict' => 'u3.codubig', 'namestate' => 'u3.desubig'))
                ->join(array('st' => Application_Model_DbTable_StreetType::NAMETABLE), 
                        'a.codtvia = st.codtvia')
                ->where('a.vchestado LIKE ?', 'A')
                ->where('a.codpais = ?', $idCountry)
                ->where('a.idcliper = ?', $idJoined);
        
        //echo $smt->assemble(); exit;
        $smt = $smt->query();
        
        $result = array('-1' => 'Entrega en tienda');
        while ($row = $smt->fetch()) {
            $result["".$row['iddenv']] = $row['namestate'].' - '.$row['nameprovince']
                     .' - '.$row['namedistrict'].' '.$row['destvia'].' '.$row['ubidenv'];
        }
        $smt->closeCursor();
        return $result;
    }
    
    public function findByIdExtend($id) {
        $where = 'codpais = a.codpais';
        
        $smt = $this->_tableAddress->getAdapter()
                ->select()
                ->from(array('a' => Application_Model_DbTable_ShipAddress::NAMETABLE))
                ->join(array('u1' => Application_Model_DbTable_Ubigeo::NAMETABLE), 
                        'a.codubig = u1.codubig AND u1.'.$where,
                        array('codstate' => 'u1.codubig', 'namedistrict' => 'u1.desubig'))
                ->join(array('u2' => Application_Model_DbTable_Ubigeo::NAMETABLE), 
                        'u1.codupar = u2.codubig AND u2.'.$where,
                        array('codprovince' => 'u2.codubig', 'nameprovince' => 'u2.desubig'))
                ->join(array('u3' => Application_Model_DbTable_Ubigeo::NAMETABLE), 
                        'u2.codupar = u3.codubig AND u3.'.$where,
                        array('coddistrict' => 'u3.codubig', 'namestate' => 'u3.desubig'))
                ->join(array('st' => Application_Model_DbTable_StreetType::NAMETABLE), 
                        'a.codtvia = st.codtvia', array('destvia' => 'st.destvia'))
                ->where('a.vchestado LIKE ?', 'A')
                ->where('a.iddenv = ?', $id);
        
        //echo $smt->assemble(); exit;
        $smt = $smt->query();
        $result = $smt->fetchAll();
        
        $smt->closeCursor();
        
        if(!empty($result)) {
            $result = $result[0];
            $nameUbigeo = "";
            
            $result['nameubig1'] = $result['namestate'];
            $result['nameubig2'] = $result['nameprovince'];
            $result['nameubig3'] = $result['namedistrict'];
            
            if(!empty($result['namestate'])) {
                $nameUbigeo .= $result['namestate'];
            } else {
                $result['nameubig1'] = $result['nameprovince'];
                $result['nameubig2'] = $result['namedistrict'];
            }
            
            
            if(!empty($result['nameprovince'])) {
                $nameUbigeo .= ($nameUbigeo != '' ? ', ' : '').$result['nameprovince'];
            }
            
            if(!empty($result['namedistrict'])) {
                $nameUbigeo .= ($nameUbigeo != '' ? ', ' : '').$result['namedistrict'];
            } 
            
            $result['ubigeoName'] = $nameUbigeo;
            
        }
        
        return $result;
    }
    
    public function findByIdOrderExtend($idOrder) {
        $where = 'codpais = a.codpais';
        
        $smt = $this->_tableAddress->getAdapter()
                ->select()
                ->from(array('a' => Application_Model_DbTable_ShipAddress::NAMETABLE))
                ->join(array('u1' => Application_Model_DbTable_Ubigeo::NAMETABLE), 
                        'a.codubig = u1.codubig AND u1.'.$where,
                        array('codstate' => 'u1.codubig', 'namedistrict' => 'u1.desubig'))
                ->join(array('u2' => Application_Model_DbTable_Ubigeo::NAMETABLE), 
                        'u1.codupar = u2.codubig AND u2.'.$where,
                        array('codprovince' => 'u2.codubig', 'nameprovince' => 'u2.desubig'))
                ->join(array('u3' => Application_Model_DbTable_Ubigeo::NAMETABLE), 
                        'u2.codupar = u3.codubig AND u3.'.$where,
                        array('coddistrict' => 'u3.codubig', 'namestate' => 'u3.desubig'))
                ->join(array('st' => Application_Model_DbTable_StreetType::NAMETABLE), 
                        'a.codtvia = st.codtvia', array('destvia' => 'st.destvia'))
                ->where('a.vchestado LIKE ?', 'A')
                ->where('a.idpedi = ?', $idOrder);
        
        //echo $smt->assemble(); exit;
        $smt = $smt->query();
        $result = $smt->fetchAll();
        
        $smt->closeCursor();
        
        if(!empty($result)) {
            $result = $result[0];
            $nameUbigeo = "";
            
            $result['nameubig1'] = $result['namestate'];
            $result['nameubig2'] = $result['nameprovince'];
            $result['nameubig3'] = $result['namedistrict'];
            
            if(!empty($result['namestate'])) {
                $nameUbigeo .= $result['namestate'];
            } else {
                $result['nameubig1'] = $result['nameprovince'];
                $result['nameubig2'] = $result['namedistrict'];
            }
            
            
            if(!empty($result['nameprovince'])) {
                $nameUbigeo .= ($nameUbigeo != '' ? ', ' : '').$result['nameprovince'];
            }
            
            if(!empty($result['namedistrict'])) {
                $nameUbigeo .= ($nameUbigeo != '' ? ', ' : '').$result['namedistrict'];
            } 
            
            $result['ubigeoName'] = $nameUbigeo;
            
        }
        
        return $result;
    }
}

