<?php

class Businessman_Model_BusinessmanSession extends Core_Model
{
    protected $_tableSession; 
    
    public function __construct() {
        parent::__construct();
        $this->_tableSession = new Application_Model_DbTable_BusinessmanSession();
    }
    
    public function sessionStart($idBusinessman, $password) {
        $active = $this->findByBusinessman($idBusinessman);
        
        if(empty($active)) {
            $idsess = md5(Core_Utils_Utils::getRamdomChars(20));
            $this->_tableSession->insert(array(
                'codempr' => $idBusinessman,
                'fecsess' => date('Y-m-d H:i:s'),
                'password' => $password,
                'idsess'=>$idsess
            ));
        } else {
            $idsess = $active['idsess'];
        }
        
        return $idsess;
    }
    
    public function sessionFinished($idBusinessman) {
        $where = $this->_tableSession->getAdapter()->quoteInto('codempr LIKE ?', $idBusinessman);
        //echo $where; exit;
        $this->_tableSession->delete($where);
    }
    
    public function findByBusinessman($idBusinessman) {
        $smt = $this->_tableSession->getAdapter()->select()
                ->from(Application_Model_DbTable_BusinessmanSession::NAMETABLE)
                ->where('codempr = ?', $idBusinessman);
                
        //echo $smt->assemble(); exit;
        $smt = $smt->query();
        
        $result = $smt->fetchAll();
        
        $smt->closeCursor();
        
        if(!empty($result)) $result = $result[0];
        
        return $result;
    }
    
    public function findBySessionCode($sessionCode) {
        $smt = $this->_tableSession->getAdapter()->select()
                ->from(Application_Model_DbTable_BusinessmanSession::NAMETABLE)
                ->where('idsess = ?', $sessionCode);
                
        //echo $smt->assemble(); exit;
        $smt = $smt->query();
        
        $result = $smt->fetchAll();
        
        $smt->closeCursor();
        
        if(!empty($result)) $result = $result[0];
        
        return $result;
    }
}

