<?php

class Admin_Model_QuizType extends Core_Model {

    protected $_tableEncuestaType;

    public function __construct() {
        $this->_tableEncuestaType = new Application_Model_DbTable_QuizType();
    }

    public function insertQuizType($idQuiz, $datas) {

        $data = array();
        for ($i = 0; $i < count($datas); $i++) {
            $data['alternativa'] = $datas[$i];
            $data['tencuesta'] = $idQuiz;
            $this->_tableEncuestaType->insert($data);
        }
    }

    public function getPairsAll($idQuizType) {
        $smt = $this->_tableEncuestaType->getAdapter()->select()
                        ->from($this->_tableEncuestaType->getName())
                        ->where("tencuesta = ?", $idQuizType)->query();
        $result = $smt->fetchAll();
        $smt->closeCursor();
        return $result;
    }
 
    public function getQuizAlterAll($idQuizType) {
        $smt = $this->_tableEncuestaType->getAdapter()->select()
                        ->from($this->_tableEncuestaType->getName())
                        ->where("idtencuestaalr = ?", $idQuizType)->query();
        $result = $smt->fetch();
        $smt->closeCursor();
        return $result;
    }
    
     

    public function insertQuizTypeCount($idQuizType,$cantidad) {
        $data['cantidad'] = $cantidad+1;
        $this->_tableEncuestaType->update($data, 'idtencuestaalr = ' . $idQuizType . '');
    }

    public function updateQuizType($idQuizType, $datas) {
        $data = array();
        for ($i = 0; $i < count($datas); $i++) {
            $data['alternativa'] = $datas[$i];
            $this->_tableEncuestaType->update($data, 'idtencuestaalr = ' . $idQuizType[$i] . '');
        }
    }

}
