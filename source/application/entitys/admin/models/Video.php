<?php

class Admin_Model_Video extends Core_Model
{
    protected $_tableVideo; 
    
    public function __construct() {
        $this->_tableVideo = new Application_Model_DbTable_Video();
    }    
  
    public function findAllBySearchType($search) {
        $select = $this->_tableVideo->getAdapter()->select()
                ->from(array('v' => $this->_tableVideo->getName()), 
                       array('v.idvideo', 'b.codtbanner', 'b.titulo', 
                             'b.descripcion', 'b.url')
                )->join(array('tb' => 'ttipobanner'), 
                        "b.codtbanner = tb.codtbanner", 
                        array('carpeta' => "CONCAT(tb.codproy, '/', tb.anchoimg, 'x', tb.altoimg)")
                )->join(array('i' => 'timagen'), 
                        "b.idimagen = i.idimagen", 
                        array('imagen' => 'i.nombre')
                )->where("b.vchestado LIKE 'A'")
                ->where("b.codtbanner LIKE ?", $idType);
        $select = $select->query();
        $result = $select->fetchAll();
        $select->closeCursor();
        return $result;
    }
    
    public function findAllByType($idType, $visible = false) {
        $select = $this->_tableVideo->getAdapter()->select()
                ->from(array('v' => $this->_tableVideo->getName()), 
                       array('v.idvideo', 'v.codtvideo', 'v.titulo', 
                             'v.descripcion', 'v.url')
                )->join(array('tv' => 'ttipovideo'), 
                        "v.codtvideo = tv.codtvideo"
                )->where("v.codtvideo LIKE ?", $idType);
        
        if (!$visible) $select->where("v.vchestado IN ('A', 'I')");
        $select->where("v.vchestado LIKE 'A'");        
        $select = $select->query();
        $result = $select->fetchAll();
        $select->closeCursor();
        
        return $result;
    }
}
