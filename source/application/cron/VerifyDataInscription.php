<?php

define("CONSOLE", true);
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV',
        (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'local'));

$_SERVER["SERVER_NAME"] = 'test';
$_SERVER["HTTP_HOST"] = 'host_test';
$_SERVER['SERVER_ADDR'] = 'fuxion.com';

require_once realpath(dirname(__FILE__) . '/../../public') . '/index.php';
$writer = new Zend_Log_Writer_Stream(
        APPLICATION_PATH . '/cron/log/VerifyDataInscription.log');
$log = new Zend_Log($writer);

$log->info("------------ Inicio cron --------------");
$log->info("");
// ini_set('max_execution_time', 120);
try {
    
    $tblDetaCiclo = new Challenge_Model_DetaCiclo();
    $data = $tblDetaCiclo->verifyDataInscription(true);
    $tblCicloParticipantes = new Challenge_Model_CicloParticipantes();
    $tblParticipantes = new Challenge_Model_Participantes();
    $viewBusinessMan = new Businessman_Model_Businessman();
    
    $date = new Zend_Date();
    $d = $date->get();
    $dtms021 = date_create();
    date_timestamp_set($dtms021, $d);
    $startone = date_format($dtms021, 'Y-m-d H:i:s');
    
    foreach ($data as $key => $value) {
        $dataCP = $tblCicloParticipantes->findRowByIdCicloPart($value['idcicparti']);
        $dataP = $tblParticipantes->findRowByIdParti($dataCP['idparti']);
//        Zend_Debug::dump($dataP);exit;
//        Zend_Debug::dump($dataCP);//exit;
//        Zend_Debug::dump($data);exit;
//        $log->info(':: ' . $value['idcicparti'] . ' ::');
//        $log->info(':: ' . $dataCP['idparti'] . ' ::');
        if($dataP['estadoofi'] != 0){
            if((($value['fecini'] <= $startone) && ($startone <= $value['fecfin'])) &&
                    (empty($value['cintura']) || empty($value['cadera']) || empty($value['cuello']))){

                $dataBusinessMan = $viewBusinessMan->findById($dataP['codemp']);

                $subject = 'Llene sus datos de Desafio Fuxion';
                $to = $dataBusinessMan['emaempr'];
                $fromName = 'Desafio Fuxion';
                $fromEmail = 'info@fuxion.com';

                $dataEmail  = "Estimado empresario : <br>";
                $dataEmail .= "Necesita llenar los datos de inscripción de \"Desafio Fuxion\", antes que su cuenta sea deshabilitado del concurso.<br>";

                $message = $dataEmail;

                $headers  = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
                $headers .= 'Date: ' . date('r', $_SERVER['REQUEST_TIME']) . "\r\n";
                $headers .= 'Message-ID: <' . $_SERVER['REQUEST_TIME'] . md5($_SERVER['REQUEST_TIME']) . '@' . $_SERVER['SERVER_NAME'] . '>' . "\r\n";
                $headers .= 'X-Originating-IP: ' . $_SERVER['SERVER_ADDR'] . "\r\n";
        //        $headers .= 'To: Online <'.$to.'>, Online2 <online@example.com>' . "\r\n";
                $headers .= 'From: '.$fromName.' <'.$fromEmail.'>' . "\r\n";
        //        $headers .= 'Reply-To: info@onlinestudioproductions.com' . "\r\n";
                $headers .= 'Return-Path: info@fuxion.com' . "\r\n";
        //        $headers .= 'Cc: birthdayarchive@example.com' . "\r\n";
        //        $headers .= 'Bcc: birthdaycheck@example.com' . "\r\n";
                $headers .= "X-Mailer: PHP/". phpversion() . "\r\n";

                mail( $to, $subject, $message, $headers );
                $log->info('Mail Enviado al usuario id :' . $dataCP['idparti']);

            }elseif(empty($value['cintura']) || empty($value['cadera']) || empty($value['cuello'])){
                $params = array( 'vchestado' => 0, 'estadoofi' => 0 );
                $tblParticipantes->update($params, $dataCP['idparti']);
                $log->info('Usuario id :' . $dataCP['idparti'] . ' ha sido deshabilitado');
            }else{
                $log->info('Ninguna incidencia');
            }
        }else
            $log->info('Ninguna incidencia 2');
    }
} catch (Exception $e) {
    $log->info('Error de Base de datos ' . $e->getMessage());
}
$log->info("");
$log->info("---------- FIN DE CRON --------");
//ini_set('max_execution_time', 30);
exit;
