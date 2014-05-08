<?php

class Challenge_Action_Helper_CompetitorMail extends Zend_Controller_Action_Helper_Abstract {

    public function mailAlert($mailHelper, $competitorIds = null) {
        $mCompetitorCycle = new Challenge_Model_CicloParticipantes();
        $competitors = $mCompetitorCycle->getWarningStateCompetitors();
        
        foreach ($competitors as $item) {
            $msg = "";
            if ($item['diaspasados'] == 6)
                $msg = "Su participación en el Desafio Fuxion esta por expirar. 
                    Registre su avance hasta el día de mañana y evite ser descalificado.";
            elseif ($item['diaspasados'] == 7) 
                $msg = "Su participación en el Desafio Fuxion esta por expirar. 
                    Registre su avance hasta el día de hoy y evite ser descalificado.";
                
            $sendMail = array(
                'email' => $item['email'],
                'message' => $msg,
                'name' => $item['nombre'],
                'lastname' => $item['apellidos'],
                'imgFuxion' => STATIC_URL.'img/fuxion.gif'
            );

            $dataMailing = array(
                'to' => $item['email'],
                'data' => Zend_Json_Encoder::encode($sendMail)
            );

            $sendMail['dataMailing'] = $dataMailing;
            $sendMail['to'] = $item['email'];

            $mailHelper->alertCompetitorWarning($sendMail);    
        }
        
        if(!empty($competitorIds)){
            $mCompetitor = new Challenge_Model_Participantes();
            $competitors2 = $mCompetitor->getByIds($competitorIds);

            foreach ($competitors2 as $item) {
                $msg = 'Te enviaron una alerta por defecto';

                $sendMail = array(
                    'email' => $item['email'],
                    'message' => $msg,
                    'name' => $item['nombre'],
                    'lastname' => $item['apellidos'],
                    'imgFuxion' => STATIC_URL.'img/fuxion.gif'
                );

                $dataMailing = array(
                    'to' => $item['email'],
                    'data' => Zend_Json_Encoder::encode($sendMail)
                );

                $sendMail['dataMailing'] = $dataMailing;
                $sendMail['to'] = $item['email'];

                $mailHelper->alertCompetitorWarning($sendMail); 
            }
        }
        
    }
    
    public function mailMessage($subject, $msg, $competitorIds, $mailHelper) {

        $mCompetitor = new Challenge_Model_Participantes();
        $competitors = $mCompetitor->getByIds($competitorIds);
        
        foreach ($competitors as $item) {
            $sendMail = array(
                'email' => $item['email'],
                'message' => $msg,
                'subject' => $subject,
                'name' => $item['nombre'],
                'lastname' => $item['apellidos'],
                'imgFuxion' => STATIC_URL.'img/fuxion.gif'
            );

            $dataMailing = array(
                'to' => $item['email'],
                'data' => Zend_Json_Encoder::encode($sendMail)
            );

            $sendMail['dataMailing'] = $dataMailing;
            $sendMail['to'] = $item['email'];

            $mailHelper->sendCompetitorMessage($sendMail);    
        }
    }
}
