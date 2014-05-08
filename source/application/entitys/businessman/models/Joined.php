<?php

class Businessman_Model_Joined extends Core_Model
{
    protected $_tableJoined; 
    
    public function __construct() {
        $this->_tableJoined = new Application_Model_DbTable_Joined();
    }
    
    
    public function insert($params) {        
        $data = array();
        if(isset($params['name'])) $data['name'] = $params['name'];
        if(isset($params['lastname'])) $data['lastname'] = $params['lastname'];
        if(isset($params['codempr'])) $data['codempr'] = $params['codempr'];
        if(isset($params['email'])) $data['email'] = trim($params['email']);
        if(isset($params['password'])) $data['password'] = $params['password'];
        if(isset($params['ndoc'])) $data['ndoc'] = $params['ndoc'];
        if(isset($params['birthdate'])) $data['birthdate'] = $params['birthdate'];
        if(isset($params['gender'])) $data['gender'] = $params['gender'];
        if(isset($params['civilstate'])) $data['civilstate'] = $params['civilstate'];
        $data['vchestado'] = 'A';
        $data['lastupdate'] = date('Y-m-d H:i:s');
        
        $this->_tableJoined->insert($data);
        return $this->_tableJoined->getAdapter()->lastInsertId();
    }
    
    public function update($idJoined, $params) {
        $where = $this->_tableJoined->getAdapter()->quoteInto('idcliper = ?', $idJoined);
        
        $data = array();
        if(isset($params['name'])) $data['name'] = $params['name'];
        if(isset($params['lastname'])) $data['lastname'] = $params['lastname'];
        if(isset($params['email'])) $data['email'] = $params['email'];
        if(isset($params['password'])) $data['password'] = $params['password'];
        if(isset($params['ndoc'])) $data['ndoc'] = $params['ndoc'];
        if(isset($params['birthdate'])) $data['birthdate'] = $params['birthdate'];
        if(isset($params['gender'])) $data['gender'] = $params['gender'];
        if(isset($params['civilstate'])) $data['civilstate'] = $params['civilstate'];
        $data['lastupdate'] = date('Y-m-d H:i:s');
        
        
        if(isset($params['lastlogin'])) $data['lastlogin'] = $params['lastlogin'];
        
        $this->_tableJoined->update($data, $where);
    }
    
    public function findByMail($email, $idBusinessman) {
        $where = $this->_tableJoined->getAdapter()->quoteInto('email LIKE ?', $email);
        $where .= " ".$this->_tableJoined->getAdapter()->quoteInto('AND codempr = ?', $idBusinessman);
        $where .= " ".$this->_tableJoined->getAdapter()->quoteInto('AND vchestado LIKE ?', 'A');
        
        $result = $this->_tableJoined->fetchAll($where);
        if (count($result) > 0) $result = $result[0];
        else return null;
        
        return $result;
    }
    
    public function existsMail($email) {
        $where = $this->_tableJoined->getAdapter()->quoteInto('email LIKE ?', $email);
        
        $result = $this->_tableJoined->fetchAll($where);
        if (count($result) > 0) return true;        
        return false;
    }
    
    public function findById($idJoined, $idBusinessman) {
        $where = $this->_tableJoined->getAdapter()->quoteInto('idcliper = ?', $idJoined);
        $where .= " ".$this->_tableJoined->getAdapter()->quoteInto('AND codempr = ?', $idBusinessman);
        $where .= " ".$this->_tableJoined->getAdapter()->quoteInto('AND vchestado LIKE ?', 'A');
        
        $result = $this->_tableJoined->fetchAll($where);
        if (!empty($result)) $result = $result[0];
        
        return $result;
    }
}

