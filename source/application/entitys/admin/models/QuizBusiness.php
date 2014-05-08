<?php

class Admin_Model_QuizBusiness extends Core_Model
{
    protected $_tableEncuestaEmpreario; 
    
    public function __construct() {
        $this->_tableEncuestaEmpreario = new Application_Model_DbTable_QuizBusiness();
    }    
  
        
 public function insertQuizEmpresarioCount($idQuiz,$idEmpresario) {
        $data = array('idencuesta' => $idQuiz, 'codempr' => $idEmpresario);
        $this->_tableEncuestaEmpreario->insert($data);
    } 
    
 public function getQuizEmpresario($idEmpresario,$idEncuesta) {
        $smt = $this->_tableEncuestaEmpreario->getAdapter()->select()
                        ->from($this->_tableEncuestaEmpreario->getName())
                        ->where("codempr = ?", $idEmpresario)
                        ->where("idencuesta = ?", $idEncuesta)->query();
        $result = $smt->fetch();
        $smt->closeCursor();
        return $result;
    }
        
}
