<?php

class Admin_Model_Quiz extends Core_Model
{
    protected $_tableEncuesta; 
    
    public function __construct() {
        $this->_tableEncuesta = new Application_Model_DbTable_Quiz();
    }    
  
    public function findById($id) {
        $smt = $this->_tableEncuesta->getAdapter()->select()
                ->from($this->_tableEncuesta->getName())
                ->where("vchestado LIKE 'A'")
                ->where('codtvideo LIKE ?', $id)
                ->query();
        
        $result = $smt->fetchAll();
        
        if(!empty($result)) {
            $result = $result[0];
        }
        $smt->closeCursor();
        return $result;
    }
    
    public function findAllByType() {
        $select = $this->_tableEncuesta->getAdapter()->select()
                ->from(array('e' => $this->_tableEncuesta->getName()), 
                       array('e.pregunta','e.encuestados')
                )->join(array('te' => 'tencuesta_alter'), 
                        "e.idencuesta = te.tencuesta",
                        array('alternativa'=>'te.alternativa',
                                 'cantidad'=>'te.cantidad',
                           'idtencuestaalr'=>'te.idtencuestaalr', )
                )->where("e.vchestado LIKE 'A'");
       $select = $select->query();
        $result = $select->fetchAll();
        $select->closeCursor();
        return $result;
        
    }
     public function insertQuizCount($idQuiz,$count){
         $encuestados= array('encuestados'=>$count+1 );
                $this->_tableEncuesta->update($encuestados,'idencuesta = '.$idQuiz.'');
        }
        
     public function getQuizAlterAll($idQuiz) {
        $smt = $this->_tableEncuesta->getAdapter()->select()
                        ->from($this->_tableEncuesta->getName())
                        ->where("idencuesta= ?", $idQuiz)->query();
        $result = $smt->fetch();
        $smt->closeCursor();
        return $result;
    }
    
    public function getQuizActivo() {
        $select = $this->_tableEncuesta->getAdapter()->select()
                        ->from($this->_tableEncuesta->getName())
                       ->where("vchestado LIKE 'A'");
        
        $select = $select->query();
        $result = $select->fetchAll();
        $select->closeCursor();
        return $result;
    }
        
}
