<?php

class Businessman_Model_RecoverAccount extends Core_Model
{
    protected $_tableRecover; 
    
    public function __construct() {
        $this->_tableRecover = new Application_Model_DbTable_RecoverAccount();
    }
    
    public function insert($params) {
        $this->downTokenState($params['idcliper']);
        
        $data = array(
            'token' => $params['token'], 
            'idcliper' => $params['idcliper'], 
            'createdate' => date('Y-m-d H:i:s'), 
            'vchestado' => 'A'
            );
        
        $this->_tableRecover->insert($data);
    }
    
    public function downTokenState($idJoined, $token = "") {
        $where = $this->_tableRecover->getAdapter()->quoteInto('idcliper = ?', $idJoined);
        
        if(trim($token) != "")
            $where .= " ".$this->_tableRecover->getAdapter()->quoteInto('AND token LIKE ?', $token);
        
        $data = array(
            'vchestado' => 'D'
        );
        
        $this->_tableRecover->update($data, $where);
    }
    
    public function getValidToken($token) {
        $where = $this->_tableRecover->getAdapter()->quoteInto('token LIKE ?', $token);
        
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
        
        $smt = $this->_tableRecover->select()->where($where)
                    ->where('createdate >  ?', $expired)
                    ->where('vchestado = ?', 'A')
                    ->query();
        
        $result = $smt->fetchAll();
        $smt->closeCursor();
        if (!empty($result)) return $result[0];
        else return false;
    }
}

