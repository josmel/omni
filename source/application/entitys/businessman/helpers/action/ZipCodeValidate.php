<?php

class Businessman_Action_Helper_ZipCodeValidate extends Zend_Controller_Action_Helper_Abstract {
    
    public function isValid($idCountry, $zipCode) {
        $state = false;
        switch($idCountry) {
            case '840' : $state = $this->isValidUSA($zipCode); break;
            default: $state = true;
        }
        
        return $state;
    }
    

    private function isValidUSA($value) {
        $zipCode = trim($value);
        //echo $zipCode; exit;
        if (empty($zipCode)) {
            return false;
        }
        
        return true;
    }
}
