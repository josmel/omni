<?php

define("CONSOLE", true);
require_once realpath(dirname(__FILE__) . '/../../public') . '/index.php';

$writer = new Zend_Log_Writer_Stream(
        APPLICATION_PATH . '/cron/log/desactivarEventosUser.log');
$log = new Zend_Log($writer);

$log->info("------------ Inicio cron --------------");
$log->info("");
// ini_set('max_execution_time', 120);
try {

    $date = new Zend_Date();
    $d = $date->get();
    $dtms021 = date_create();
    date_timestamp_set($dtms021, $d);
    $startone = date_format($dtms021, 'Y-m-d H:i:s');

    $objEU = new Application_Model_Event();
    $echo = $objEU->listEventUsers();
    foreach ($echo as $vat) {
        if ($vat['dateEvent'] < $startone) {
            if (date('Y-m-d H:i:s', (strtotime($vat['dateEvent'] . " + 1 hours "))) < $startone) {
                echo $vat['idUser'] . '\n';
                $log->info('desactivo evento del  title: ' . $vat['title'] . ' idEvent: ' . $vat['idEvent']);
                $objEU->updateEvent($vat['idEvent']);
            }
        }
    }
} catch (Exception $e) {
    $log->info('Error de Base de datos ' . $e->getMessage());
}
$log->info("");
$log->info("---------- FIN DE CRON --------");
//ini_set('max_execution_time', 30);
exit;
