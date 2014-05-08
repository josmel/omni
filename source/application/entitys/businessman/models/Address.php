<?php

class Businessman_Model_Address extends Core_Model
{
    protected $_tableAddress; 
    
    public function __construct() {
        $this->_tableAddress = new Application_Model_DbTable_Address();
    }
    
    public function insert($params) {
        $data = array();
        if(isset($params['codempr'])) $data['codempr'] = $params['codempr'];
        if(isset($params['codtvia'])) $data['codtvia'] = $params['codtvia'];
        if(isset($params['zipcode'])) $data['zipcode'] = $params['zipcode'];
        if(isset($params['desdire'])) $data['desdire'] = $params['desdire'];
        if(isset($params['codubig'])) $data['codubig'] = $params['codubig'];
        if(isset($params['nomcont'])) $data['nomcont'] = $params['nomcont'];
        if(isset($params['idtdoc'])) $data['idtdoc'] = $params['idtdoc'];
        if(isset($params['ndoc'])) $data['ndoc'] = $params['ndoc'];
        if(isset($params['telefono'])) $data['telefono'] = $params['telefono'];
        if(isset($params['codpais'])) $data['codpais'] = $params['codpais'];
        if(isset($params['tvidire'])) $data['tvidire'] = $params['tvidire'];
        if(isset($params['numdire'])) $data['numdire'] = $params['numdire'];
        if(isset($params['intdire'])) $data['intdire'] = $params['intdire'];
        if(isset($params['refdire'])) $data['refdire'] = $params['refdire'];          
        //if(isset($params['vchestado'])) $data['vchestado'] = $params['vchestado'];
        if(isset($params['pediestado'])) $data['pediestado'] = $params['pediestado'];
        if(isset($params['vchusumodif'])) $data['vchusumodif'] = $params['vchusumodif'];
        if(isset($params['vchequipo'])) $data['vchequipo'] = $params['vchequipo'];
        if(isset($params['vchprograma'])) $data['vchprograma'] = $params['vchprograma'];
        $data['vchestado'] = 'D';
        
        $this->_tableAddress->insert($data);
        return $this->_tableAddress->getAdapter()->lastInsertId();
    }
    
    public function update($idAddress, $params) {
        $where = $this->_tableAddress->getAdapter()->quoteInto('iddire = ?', $idAddress);

        $data = array();
        if(isset($params['codempr'])) $data['codempr'] = $params['codempr'];
        if(isset($params['codtvia'])) $data['codtvia'] = $params['codtvia'];
        if(isset($params['zipcode'])) $data['zipcode'] = $params['zipcode'];  
        if(isset($params['desdire'])) $data['desdire'] = $params['desdire'];
        if(isset($params['codubig'])) $data['codubig'] = $params['codubig'];
        if(isset($params['nomcont'])) $data['nomcont'] = $params['nomcont'];
        if(isset($params['idtdoc'])) $data['idtdoc'] = $params['idtdoc'];
        if(isset($params['ndoc'])) $data['ndoc'] = $params['ndoc'];
        if(isset($params['telefono'])) $data['telefono'] = $params['telefono'];
        if(isset($params['codpais'])) $data['codpais'] = $params['codpais'];
        if(isset($params['tvidire'])) $data['tvidire'] = $params['tvidire'];
        if(isset($params['numdire'])) $data['numdire'] = $params['numdire'];
        if(isset($params['intdire'])) $data['intdire'] = $params['intdire'];
        if(isset($params['refdire'])) $data['refdire'] = $params['refdire'];          
        //if(isset($params['vchestado'])) $data['vchestado'] = $params['vchestado'];
        if(isset($params['pediestado'])) $data['pediestado'] = $params['pediestado'];
        if(isset($params['vchusumodif'])) $data['vchusumodif'] = $params['vchusumodif'];
        if(isset($params['vchequipo'])) $data['vchequipo'] = $params['vchequipo'];
        if(isset($params['vchprograma'])) $data['vchprograma'] = $params['vchprograma'];
        $data['tmsfecmodif'] = date('Y-m-d H:i:s');
        
        $this->_tableAddress->update($data, $where);
    }
    
    public function findById($id) {
        //$where = $this->_tableAddress->getAdapter()->quoteInto('vchestado LIKE ?', 'A');
        //$where .= " ".$this->_tableAddress->getAdapter()->quoteInto('AND iddire = ?', $id);
        $where = $this->_tableAddress->getAdapter()->quoteInto(' iddire = ?', $id);

        $result = $this->_tableAddress->fetchAll($where);
        
        if(!empty($result)) $result = $result[0];
        
        return $result;
    }
    
    public function findAllByBusinessman($idBusinessman, $idCountry) {
        $where = $this->_tableAddress->getAdapter()->quoteInto('codpais = ?', $idCountry);
        
        $smt = $this->_tableAddress->getAdapter()
                ->select()
                ->from(array('a' => Application_Model_DbTable_Address::NAMETABLE))
                ->joinLeft(array('u1' => Application_Model_DbTable_Ubigeo::NAMETABLE), 
                        'a.codubig = u1.codubig AND u1.'.$where,
                        array('codstate' => 'u1.codubig', 'namedistrict' => 'u1.desubig', 'iva_1' => 'u1.ivaubig'))
                ->joinLeft(array('u2' => Application_Model_DbTable_Ubigeo::NAMETABLE), 
                        'u1.codupar = u2.codubig AND u2.'.$where,
                        array('codprovince' => 'u2.codubig', 'nameprovince' => 'u2.desubig', 'iva_2' => 'u2.ivaubig'))
                ->joinLeft(array('u3' => Application_Model_DbTable_Ubigeo::NAMETABLE), 
                        'u2.codupar = u3.codubig AND u3.'.$where,
                        array('coddistrict' => 'u3.codubig', 'namestate' => 'u3.desubig', 'iva_3' => 'u3.ivaubig'))
                ->joinLeft(array('st' => Application_Model_DbTable_StreetType::NAMETABLE), 
                        'a.codtvia = st.codtvia')
                ->where('a.codpais = ?', $idCountry)
                ->where('a.codempr = ?', $idBusinessman);
        
        //echo $smt->assemble(); exit;
        $smt = $smt->query();
        
        $result = array();
        while($row = $smt->fetch()) {
            $nameUbigeo = "";
            
            $row['nameubig1'] = $row['namestate'];
            $row['nameubig2'] = $row['nameprovince'];
            $row['nameubig3'] = $row['namedistrict'];
            
            if(!empty($row['namestate'])) {
                $nameUbigeo .= $row['namestate'];
                $iva = $row['iva_1']; 
            } else {
                $row['nameubig1'] = $row['nameprovince'];
                $row['nameubig2'] = $row['namedistrict'];
            }
            
            
            if(!empty($row['nameprovince'])) {
                $nameUbigeo .= ($nameUbigeo != '' ? ', ' : '').$row['nameprovince'];
                $iva = $row['iva_2'];
            }
            
            if(!empty($row['namedistrict'])) {
                $nameUbigeo .= ($nameUbigeo != '' ? ', ' : '').$row['namedistrict'];
                $iva = $row['iva_3'];
            } 
            
            $row['ubigeoName'] = $nameUbigeo;
            $row['iva'] = $iva;
            if(trim($row['codubig']) != '0000000000')
                $result[$row['iddire']] = $row;
        }
        
        $smt->closeCursor();
        
        return $result;
    }
    
    
    public function findByIdExtend($id) {
        $where = 'codpais = a.codpais';
        
        $smt = $this->_tableAddress->getAdapter()
                ->select()
                ->from(array('a' => Application_Model_DbTable_Address::NAMETABLE))
                ->joinLeft(array('td' => Application_Model_DbTable_DocumentType::NAMETABLE), 
                        'a.idtdoc = td.idtdoc',
                        array('destdoc' => 'td.destdoc'))
                ->joinLeft(array('u1' => Application_Model_DbTable_Ubigeo::NAMETABLE), 
                        'a.codubig = u1.codubig AND u1.'.$where,
                        array('codstate' => 'u1.codubig', 'namedistrict' => 'u1.desubig'))
                ->joinLeft(array('u2' => Application_Model_DbTable_Ubigeo::NAMETABLE), 
                        'u1.codupar = u2.codubig AND u2.'.$where,
                        array('codprovince' => 'u2.codubig', 'nameprovince' => 'u2.desubig'))
                ->joinLeft(array('u3' => Application_Model_DbTable_Ubigeo::NAMETABLE), 
                        'u2.codupar = u3.codubig AND u3.'.$where,
                        array('coddistrict' => 'u3.codubig', 'namestate' => 'u3.desubig'))
                ->joinLeft(array('st' => Application_Model_DbTable_StreetType::NAMETABLE), 
                        'a.codtvia = st.codtvia', array('destvia' => 'st.destvia'))
                //->where('a.vchestado LIKE ?', 'A')
                ->where('a.iddire = ?', $id);
        
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

