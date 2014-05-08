<?php

class Challenge_Action_Helper_SaveInscription extends Zend_Controller_Action_Helper_Abstract {

    public function saveInscription($auth, $step) {
        $tblParticipantes = new Challenge_Model_Participantes();
        $tblMentor = new Challenge_Model_Mentor();
        $tblCicloParticipantes = new Challenge_Model_CicloParticipantes();
        $tblDetaCiclo = new Challenge_Model_DetaCiclo();
        
        $dbAdapter = Zend_Registry::get('dbChallenge');
        $idBusinessman = $auth->auth['codemp'];
        $dbAdapter->beginTransaction();
        try {
            if($auth->isParticipante){
                $idParticipante = $auth->idParticipante;
            }  else {
                $dataSaveParticipantes = array(
                    'codemp' => $auth->auth['codemp'],
                    'seudonimo' => $step->twoA['nickemp'],
                    'sexo' => $auth->auth['sexemp'],
                    'email' => $step->twoA['email'],
                    'nombre' => $auth->auth['name'],
                    'edad' => $step->twoA['edad'],
                    'apellidos' => $auth->auth['lastName'],
                    'estadoofi' => $auth->auth['officeActive'],
                    'vchestado' => 1,
                    'vchusucrea' => $auth->auth['codemp'],
                    'tmsfeccrea' => date('Y-m-d H:i:s'),
                );
                $save = $tblParticipantes->insert($dataSaveParticipantes);
                $idParticipante = $save;
            }
            $dataMentor = $tblMentor->findRowByCodemp($step->twoA['codmentor'], true);
            if($dataMentor != false){
                $idMentor = $dataMentor['idmentor'];
            }else{
                $dataSaveMentor = array(
                    'codemp' => $step->twoA['codmentor'],
                    'nommentor' => $step->twoA['dataMentor']['nomemp'],
                    'apepaterno' => $step->twoA['dataMentor']['apepaterno'],
                    'apematerno' => $step->twoA['dataMentor']['apematerno'],
                    'telefono' => $step->twoA['dataMentor']['telefono'],
                    'celular' => $step->twoA['dataMentor']['celular'],
                    'correo' => $step->twoA['dataMentor']['email'],
                    'vchestado' => 1,
                    'vchusucrea' => $auth->auth['codemp'],
                    'tmsfeccrea' => date('Y-m-d H:i:s'),
                );
                $idMentor = $tblMentor->insert($dataSaveMentor);
            }

            $fechainiciociclo = new DateTime($auth->ciclo['fecinicio']);
            $fechafinciclo = new DateTime($auth->ciclo['fecfin']);
            $interval = $fechainiciociclo->diff($fechafinciclo);
            $diasciclo = $interval->format('%a');
            $fechaCicloParticipante = date('Y-m-d H:i:s');
            $fechafinCicloParticipante = new DateTime($fechaCicloParticipante);
            $fechafinCicloParticipante->modify("+$diasciclo day");
            $fechaFCP = $fechafinCicloParticipante->format('Y-m-d H:i:s');
            $dataSaveCicloParticipante = array(
                'idciclo' => $auth->ciclo['idciclo'],
                'idparti' => $idParticipante,
                'idmentor' => $idMentor,
    //                'tiembaja' => $step->oneC['tiembaja'],
                'kilobaja' => $step->oneC['kilobaja'],
                'indgrasa' => $step->oneA['indgrasa'],
                'espalda' => $step->oneC['espalda'],
                'cintura' => $step->oneA['cintura'],
                'cadera' => $step->oneC['cadera'],
                'motivo' => $step->oneC['motivo'],
                'compromiso' => $step->oneC['compromiso'],
                'fecini' => $fechaCicloParticipante,
                'fecfin' => $fechaFCP,
                'nrosemana' => $auth->ciclo['nrosemana'],
                'vchestado' => 1,
                'vchusucrea' => $auth->auth['codemp'],
                'tmsfeccrea' => date('Y-m-d H:i:s'),
            );
            
            $idCicloParticipante = $tblCicloParticipantes->insert($dataSaveCicloParticipante);

            $fechafinDetaCiclo = new DateTime($fechaCicloParticipante);
            $fechafinDetaCiclo->modify("+6 day");
            $fechaFDC = $fechafinDetaCiclo->format('Y-m-d H:i:s');
            $dataSaveDetaCiclo = array(
                'idcicparti' => $idCicloParticipante,
                'idtipmusc' => Core_Utils_BodyStats::reversecontextura($step->oneA['idtipmusc']),
                'deporte' => $step->oneA['deporte'],
                'talla' => $step->oneA['talla'],
                'muneca' => '',
                'peso' => $step->oneA['peso'],
                'indgrasa' => $step->oneA['indgrasa'],
                'espalda' => $step->oneA['espalda'],
                'cintura' => $step->oneA['cintura'],
                'cadera' => $step->oneA['cadera'],
                'pecho' => $step->oneA['pecho'],
                'cuello'=>$step->oneA['cuello'],
                'fotowincha' => $step->upload['wincha'],
                'fotofrente' => $step->upload['frente'],
                'fotootros' => $step->upload['otro'],
                'fotoperfil' => $step->upload['perfil'],
    //                'codfactcompra' => $step->oneC['motivo'],
    //                'cantcompra' => $step->oneC['compromiso'],
                'fecini' => $fechaCicloParticipante,
                'fecfin' => $fechaFDC,
                'semana' => 0,
                'vchestado' => 1,
                'vchusucrea' => $auth->auth['codemp'],
                'tmsfeccrea' => date('Y-m-d H:i:s'),
            );
            $idDetaCiclo = $tblDetaCiclo->insert($dataSaveDetaCiclo);
            $dbAdapter->commit();
            
            $auth->idParticipante = $idParticipante;
            $auth->isParticipante = TRUE;
                
            return true;
        } catch (Exception $ex) {
            $dbAdapter->rollBack();
            
            $stream = @fopen(LOG_PATH.'/errorsaveinscription.log', 'a', false);
            if (!$stream) {
                echo "Error al abrir log.";
            }
        
            $writer = new Zend_Log_Writer_Stream(LOG_PATH.'/errorsaveinscription.log');
            $logger = new Zend_Log($writer);

            $logger->info('***********************************');
            $logger->info('Codigo Empresario: '.$idBusinessman);
            $logger->info('Error Message: '.$ex->getMessage());
            $logger->info('***********************************');
            $logger->info('');
        
            return false;
        }
        return false;
    }
}