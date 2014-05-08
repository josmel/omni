<?php

class Admin_Model_Blog extends Core_Model
{
    protected $_tableBlog; 
    
    public function __construct() {
        $this->_tableBlog = new Application_Model_DbTable_Blog();
    }
    
    
     
    

    public function findById($id) {
        $where = $this->_tableBlog->getAdapter()->quoteInto('vchestado LIKE ?', 'A');
        $where .= " ".$this->_tableBlog->getAdapter()->quoteInto('AND idblog = ?', $id);
        
        $result = $this->_tableBlog->fetchAll($where);
        
        if(!empty($result)) $result = $result[0];
        
        return $result;
    }
    
    public function findAll() {
        $where = $this->_tableBlog->getAdapter()->quoteInto('vchestado LIKE ?', 'A');
        
        $result = $this->_tableBlog->fetchAll($where);
        
        return $result;
    }
      public function statusBlog($id) {
            $smt = $this->_tableBlog->getAdapter()->select()
                        ->from($this->_tableBlog->getName(),array('vchestado'))
                        ->where("idblog = ?", $id)->query();
        $result = $smt->fetch();
        $smt->closeCursor();
        return $result;
    }
      public function updateStatusBlog($id, $data) {
                $this->_tableBlog->update(array('vchestado'=>$data),'idblog = '.$id.'');
        }
  public function insertBlogCron($dataBlog) {
      
       $where = $this->_tableBlog->getAdapter()
                          ->quoteInto('fecpubli <= ?', date('Y-m-d H:i:s'));
       $this->_tableBlog->delete($where);
      
        $data = array();
        for ($i = 0; $i < count($dataBlog); $i++) {
            $data['titulo'] = $dataBlog[$i]['titulo'];
             $data['descripcion'] = $dataBlog[$i]['descripcion'];
              $data['url'] = $dataBlog[$i]['url'];
              $data['vchestado'] = 'A';
              $data['tmsfecmodif'] = date('Y-m-d H:i:s');
               $data['fecpubli'] = date('Y-m-d H:i:s');
            $this->_tableBlog->insert($data);
        }
    }
}

