<?php

class Biller_Action_Helper_RucValidate extends Zend_Controller_Action_Helper_Abstract {
    
    public function isValid($idCountry, $ruc) {
        $state = false;
        switch($idCountry) {
            case '604' : $state = $this->isValidPeru($ruc); break;
            default: $state = true;
        }
        
        return $state;
    }
    

    private function isValidPeru($value) {
        $factor = "5432765432";
        $ruc = trim($value);

        if ((!is_numeric($ruc)) || (strlen($ruc) != 11)) {
            return false;
        }

        $dig_valid= array("10", "20" ,"17", "15");
        $dig=substr($ruc, 0, 2);

        if (!in_array($dig, $dig_valid, true)) {
            return false;
        }
        
        $dig_verif = substr($ruc, 10, 1);

        for ($i = 0; $i < 10; $i++) {
            $arr[] = substr($ruc, $i, 1) * substr($factor, $i, 1);
        }
        $suma = 0;

        foreach ($arr as $a) {
            $suma = $suma + $a;
        }

    //Calculamos el residuo
        $residuo = $suma%11;
        $resta = 11 - $residuo;
        $dig_verif_aux = substr($resta, -1);

        if ($dig_verif == $dig_verif_aux) {
            return true;
        } else {
            return false;
        }
    }
}
