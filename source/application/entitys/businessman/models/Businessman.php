<?php

class Businessman_Model_Businessman extends Core_Model
{
    protected $_tableBusinessman; 
    protected $_viewBusinessman; 
    
    public function __construct() {
        $this->_tableBusinessman = new Application_Model_DbTable_Businessman();
        $this->_viewBusinessman = new Application_Model_DbTable_VBusinessman();
    }
    
    function getBySubdomain($subDomain) {
        return $this->_tableBusinessman->getBySubDomain($subDomain);
    }
    
    function getBySubdomainFromView($subDomain) {
        return $this->_viewBusinessman->getBySubDomain($subDomain);
    }
    
    public function findByMail($email) {
        $where = $this->_tableBusinessman->getAdapter()->quoteInto('emaempr LIKE ?', $email);
        $where .= " ".$this->_tableBusinessman->getAdapter()->quoteInto('AND vchestado LIKE ?', 'A');
        
        $result = $this->_tableBusinessman->fetchAll($where);
        if (count($result) > 0) $result = $result[0];
        else return null;
        
        return $result;
    }
    
    public function update($codempr, $params) {
        $data = array(
            'token' =>'', 
            'tmsfecmodif' => date('Y-m-d H:i:s'), 
            'createdatetoken' => date('Y-m-d H:i:s'), 
             'claempr' => $params['claempr']
            //'vchusumodif' => $codempr
            );
       $this->_tableBusinessman->update($data, 'codempr = ' . $codempr . '');
    }
    
    
    public function updatePicture($codempr, $params) {
              //  var_dump($params);exit;
       $this->_tableBusinessman->update($params, 'codempr = ' . $codempr . '');
    }
     
    public function insertToken($params) {
        $data = array(
            'token' => $params['token'], 
            'tmsfecmodif' => date('Y-m-d H:i:s'), 
            'createdatetoken' => date('Y-m-d H:i:s'), 
            'vchusumodif' => $params['codempr']
            );
       $this->_tableBusinessman->update($data, 'codempr = ' . $params['codempr'] . '');
    }
    
          
    
    public function getValidToken($token) {
        $where = $this->_tableBusinessman->getAdapter()->quoteInto('token LIKE ?', $token);
        
        $expired = new Zend_Date(
                date('Y-m-d H:i:s'), 
                Zend_Date::YEAR.'-'.Zend_Date::MONTH.'-'.Zend_Date::DAY.' '
                .Zend_Date::HOUR.':'.Zend_Date::MINUTE.':'.Zend_Date::SECOND
            );
        
        $expired = $expired->addHour(-2); 
        $expired = $expired->get(
                Zend_Date::YEAR.'-'.Zend_Date::MONTH.'-'.Zend_Date::DAY.' '
                .Zend_Date::HOUR.':'.Zend_Date::MINUTE.':'.Zend_Date::SECOND
            );   
        
        $smt = $this->_tableBusinessman->select()->where($where)
                    ->where('createdatetoken >  ?', $expired)
                //    ->where('vchestado = ?', 'A')
                    ->query();
        
        $result = $smt->fetchAll();
        $smt->closeCursor();
        if (!empty($result)) return $result[0];
        else return false;
    }
    
    public function findById($codempr) {
        $where = $this->_tableBusinessman->getAdapter()->quoteInto('codempr = ?', $codempr);
        $where .= " ".$this->_tableBusinessman->getAdapter()->quoteInto('AND vchestado LIKE ?', 'A');
        $result = $this->_tableBusinessman->fetchAll($where);
        if (!empty($result)) $result = $result[0];

        return $result;
    } 

    public function findDataViewById($id) {
        return $this->_viewBusinessman->getById($id);
    } 
    
    public function updateBusinessmanPass($passBusinessman, $codempr) {
        $data = array(
            'tmsfecmodif' => date('Y-m-d H:i:s'), 
            'claempr' => $passBusinessman,
            'vchusumodif' => $codempr
        );
        $this->_tableBusinessman->update($data, 'codempr = ' . $codempr . '');
    }
    
    public function hasFuxionCard($cardNumber) {
        $smt = $this->_tableBusinessman->select()
                    ->where('ntfempr = ?', $cardNumber);
        //echo $smt->assemble(); exit;
        $smt = $smt->query();
        $result = $smt->fetchAll();
        $smt->closeCursor();
        
        if (!empty($result)) return true;
        
        return false;
    }
    
    public function setFuxionCardLog($idBusinessman = null, 
            $cardNumber = null, $idOrder = null, $amount = null, 
            $dolarFactor = null,$typePay=null)
    {
        //Guardar Log
        

        $stream = @fopen(LOG_PATH.'/fuxioncard.log', 'a', false);
        if (!$stream) {
            echo "Error al guardar.";
        }

        $writer = new Zend_Log_Writer_Stream(LOG_PATH.'/fuxioncard.log');
        $logger = new Zend_Log($writer);
        
        $logger->info('***********************************');
        $logger->info('Codigo Empresario: '.$idBusinessman);
        $logger->info('"'.$typePay.'": '.$cardNumber);
        $logger->info('Codigo Pedido: '.$idOrder);
        $logger->info('Monto: '.$amount);
        $logger->info('Cambio Dolar: '.$dolarFactor);
        $logger->info('***********************************');
        $logger->info('');
    }
    
    public function updateRuc($ruc, $codempr) {
        $data = array(
            'tmsfecmodif' => date('Y-m-d H:i:s'), 
            'rucempr' => $ruc,
            'vchusumodif' => $codempr
        );
        $this->_tableBusinessman->update($data, 'codempr = ' . $codempr . '');
    }
    
    public function updateChallenge($idBusinessman, $params) {
        $data = array(
            'tmsfecmodif' => date('Y-m-d H:i:s'), 
            'nomempr' => $params['nomemp'],
            'appempr' => $params['apepaterno'],
            'apmempr' => $params['apematerno'],
            'emaempr' => $params['email']
        ); 
        $where = $this->_tableBusinessman->getAdapter()->quoteInto("codempr = ?", $idBusinessman);
        $this->_tableBusinessman->update($data, $where);
    }
}

