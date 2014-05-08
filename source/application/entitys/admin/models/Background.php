<?php

class Admin_Model_Background extends Core_Model
{
    protected $_tableBackground; 
    
    public function __construct() {
        $this->_tableBackground = new Application_Model_DbTable_Background();
    }    
  

    public function findAllByType($idType, $visible = false) {
        $select = $this->_tableBackground->getAdapter()->select()
                ->from(array('b' => $this->_tableBackground->getName()), 
                       array('b.idfondo', 'b.codtfondo', 'b.titulo', 
                             'b.idimagen',  'b.vchestado')
                )->join(array('tb' => 'ttipofondo'), 
                        "b.codtfondo = tb.codtfondo", 
                        array('carpeta' => "tb.codproy")
                )->join(array('i' => 'timagen'), 
                        "b.idimagen = i.idimagen", 
                        array('imagen' => 'i.nombre')
                )->where("b.codtfondo LIKE ?", $idType);
        if (!$visible) $select->where("b.vchestado IN ('A', 'I')");
        else $select->where("b.vchestado LIKE 'A'");
      
        $select = $select->query();
        $result = $select->fetchAll();
        $select->closeCursor();
        
        if(!empty($result)) {
            $result = $result[0];
        }
           
        return $result;
    }
    
}
