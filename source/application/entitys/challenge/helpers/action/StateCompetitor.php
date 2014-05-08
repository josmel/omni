<?php
class Challenge_Action_Helper_StateCompetitor extends Zend_Controller_Action_Helper_Abstract {

    public function stateCompetitor($date, $state) {
        $stateP = 2;
        $fechaIni = new DateTime(substr($date, 0, 10));
        $fechaIni->modify("+1 day");
        $fechaIniDC = $fechaIni->format('Y-m-d');
        $fechaFin = new DateTime($fechaIniDC);
        $fechaFin->modify("+6 day");
        $fechaFinDC = $fechaFin->format('Y-m-d');
        $difFinNow = round(((strtotime($fechaFinDC) - strtotime(date('Y-m-d')))/(60 * 60 * 24)));
        ///echo $difFinNow; exit;
        if($difFinNow > 1 && $state == 'A'){
            $stateP = 1;
        }elseif(($difFinNow <= 1 && $difFinNow >= 0) && $state == 'A'){
            $stateP = 2;
        }elseif($state != 'A'){
            $stateP = 0;
        }
        return $stateP;
    }
}