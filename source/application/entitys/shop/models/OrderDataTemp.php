<?php

class Shop_Model_OrderDataTemp extends Core_Model {
    protected $_table; 
    
    public function __construct() {
        parent::__construct();
        $this->_table = new Application_Model_DbTable_OrderDataTemp();
    }

    public function insert($params) {
        $data = array();
        if(isset($params['idpedi'])) $data['idpedi'] = $params['idpedi'];
        if(isset($params['bizpay'])) $data['bizpay'] = $params['bizpay'];
        if(isset($params['data'])) $data['data'] = $params['data'];
        $data['fecregistro'] = date('Y-m-d H:i:s');
        
        $this->_table->insert($data);
        $id = $this->_table->getAdapter()->lastInsertId();
    }
    
    public function updateData($idOrder, $data) {
        $where = $this->_table->getAdapter()->quoteInto('idpedi = ?', $idOrder);
        
        $data = array('data' => $data, 'fecupdate' => date('Y-m-d H:i:s'));
        
        $this->_table->update($data, $where);
        $id = $this->_table->getAdapter()->lastInsertId();
    }
    
    function findByBizpay($bizpay) {
        $smt = $this->_table->select()
                    ->where('bizpay = ?', $bizpay)
                    ->order('fecregistro DESC');
        //echo $smt->assemble(); exit;
        $smt = $smt->query();
        $result = $smt->fetchAll();
        $smt->closeCursor();
        
        if (!empty($result)) $result = $result[0];
        return $result;
    }
    
    function findByIdOrder($idOrder) {
        $smt = $this->_table->select()
                    ->where('idpedi = ?', $idOrder)
                    ->order('fecregistro DESC');
        //echo $smt->assemble(); exit;
        $smt = $smt->query();
        $result = $smt->fetchAll();
        $smt->closeCursor();
        
        if (!empty($result)) $result = $result[0];
        return $result;
    }
    
    function delete($idOrder) {
        $where = $this->_table->getAdapter()->quoteInto('idpedi = ?', $idOrder);
        
        $this->_table->delete($where);
    }
}

