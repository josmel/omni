<?php

class Shop_Action_Helper_SelectorPay extends Zend_Controller_Action_Helper_Abstract {

    /**
     * 
     * @param string $formulario
     * @param object Objeto del pago
     */
    public function payMethod($method,$objPay)
    {           
        $colMethodPay=array(            
            'TP004'=>'alignet',
            'TP011'=>'tpp'
        );
        switch ($method){
            case 'TP004' : $return= $objPay->getMensaje();
                
                exit;
                break;
            default : $return='';    
        }
        
    }
}
