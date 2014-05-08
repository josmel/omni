<?php

class Shop_Action_Helper_OrderMail extends Zend_Controller_Action_Helper_Abstract {

    public function mailOrderComplete($businessman, $joined, $address, $carrito, $mailHelper) {
        $desAddress = $address['destvia'].' '.$address['ubidenv'].', '.
                $address['namedistrict'].', '.$address['nameprovince'].', '.
                $address['namestate'].', '.$businessman['nompais'];
        $sendMail = array(
            'email' => $joined->email,
            'cart' => $carrito->getCartData(),
            'name' => $joined->name,
            'urlProduct' => SITE_URL."product/",
            'lastname' => $joined->lastname,
            'symbol' => $businessman['simbolo'],
            'imgFuxion' => STATIC_URL.'img/fuxion.gif',
            'address' => $desAddress,
            'idOrder' => $carrito->getIdOrder()
        );

        $dataMailing = array(
            'to' => $joined->email,
            'data' => Zend_Json_Encoder::encode($sendMail)
        );

        $sendMail['dataMailing'] = $dataMailing;
        $sendMail['to'] = $joined->email;

        $mailHelper->orderComplete($sendMail);
    }
    
    public function officeOrderComplete($businessman, $address, $carrito, $fMessages, $mailHelper) {
        if ($address == 'SHOP') $desAddress = 'Entrega en tienda.';
        else $desAddress = $address['destvia'].' '.$address['desdire'].', '.
                $address['ubigeoName'].', '.$businessman['nompais'];
        
        if (empty($businessman['emaempr'])) {
            $fMessages->warning("Para recibir emails debe registrar su dirección de correo", 'TEMP');
            return;
        }
        
        $sendMail = array(
            'email' => $businessman['emaempr'],
            'cart' => $carrito->getCartData(),
            'name' => $businessman['nomempr'],
            'urlProduct' => SITE_URL."product/",
            'lastname' => $businessman['appempr'].' '.$businessman['apmempr'],
            'symbol' => $businessman['simbolo'],
            'imgFuxion' => STATIC_URL.'img/fuxion.gif',
            'address' => $desAddress,
            'idOrder' => $carrito->getIdOrder()
        );

        $dataMailing = array(
            'to' => $businessman['emaempr'],
            'data' => Zend_Json_Encoder::encode($sendMail)
        );

        $sendMail['dataMailing'] = $dataMailing;
        $sendMail['to'] = $businessman['emaempr'];

        $mailHelper->officeOrderComplete($sendMail);
    }
}
