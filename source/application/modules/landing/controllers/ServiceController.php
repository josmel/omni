<?php

class Landing_ServiceController extends Core_Controller_ActionLanding
{

    public function init()
    {
        parent::init();
    }

    public function alignetAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $params=$this->getAllParams();
        Zend_Debug::dump($params);
        $dataConfig=Zend_Registry::get('config');        
        $data=$dataConfig['pasarela']['alignet'];
        $vector=$data['vector'];
$llavePrivadaCifrada = "-----BEGIN RSA PRIVATE KEY-----\n".
"MIICXgIBAAKBgQC3gZWtxCGOFjzelx1ia1JI2zXmJgX/hHnAdzOATcyrooWf7Zl/\n".
"PW3yyfKMPYWFU5jAadnGC+oghHWcopBCBB9Hsn8RGJXF8kcLMQUhuUuBm0wZi0n6\n".
"LCu4aCJTJm8ubcQkT9aC9+wEHb4XXwuiiTBOjTLhGustZI84Slb5JgAtRQIDAQAB\n".
"AoGBAIzgJpBn6XqzB7e6Owy7Z0gXQaGVvzUR9oYS/K6bC20YL40/Un+kISwqbNHM\n".
"yRw5uSK+IDkyHGMqTUAabLTBhWCwkUHQxwu+W8GB44/s13HS/vXPFCgypy1zj5Wp\n".
"MFt6HiHbDRD+v9r8XX6TuTvBpsfb97JLnP8yhtMhOYfPSTMBAkEA2v/Smp+hf+xv\n".
"etVitnSEjI7ewXsWfIfwSqQ8agStK8ac5UwmI8J/bsGQjkV25pjo+pM/5AWqZ2rB\n".
"Oi7+IuHJ6QJBANaCnE8awTf77UH8Xa5WXUYtP5oCYtuQkWR1SkQO+V5ar01RreVL\n".
"iihJsLIfmqqY+gN2fB1LyqJBioUz8VxXUv0CQQCzdhpWfWOx2cXNCdoXnEoWiOl/\n".
"8ecOa7N85zNeybBDBEIEIS/L9BHLaDGWdNQnybx5wnsD16S5lKD9lg7J9O8pAkB8\n".
"mdmZyXy6vEeQsLrp7Zi4jsgG+aPazhrmM4s0BU6slBiH3Q4Zxss1o7hTfzAkMD/p\n".
"iwWFLuVBYrjHruhgLwkxAkEAxOVXfWi3WQyrCvvVgD2s4xJWjEglWbjPlQ+jgxKv\n".
"02O41SPvVZdkkWGkr3FFGu9k9tx++oxIPhvHgTBSdXTLKQ==\n".
"-----END RSA PRIVATE KEY-----\n";
$llavePublicaFirma="-----BEGIN PUBLIC KEY-----\n".
"MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCvJS8zLPeePN+fbJeIvp/jjvLW\n".
"Aedyx8UcfS1eM/a+Vv2yHTxCLy79dEIygDVE6CTKbP1eqwsxRg2Z/dI+/e14WDRs\n".
"g0QzDdjVFIuXLKJ0zIgDw6kQd1ovbqpdTn4wnnvwUCNpBASitdjpTcNTKONfXMtH\n".
"pIs4aIDXarTYJGWlyQIDAQAB\n".
"-----END PUBLIC KEY-----";
       
        $arrayIn['IDACQUIRER'] = $_POST['IDACQUIRER'];
        $arrayIn['IDCOMMERCE'] = $_POST['IDCOMMERCE'];
        $arrayIn['XMLRES'] = $_POST['XMLRES'];
        $arrayIn['DIGITALSIGN'] = $_POST['DIGITALSIGN'];
        $arrayIn['SESSIONKEY'] = $_POST['SESSIONKEY'];
        $arrayOut = '';        
        $pluginVPOS = new Core_Utils_VposPlugin();
        $rptaPlugin=$pluginVPOS->VPOSResponse($arrayIn, $arrayOut, $llavePublicaFirma,
            $llavePrivadaCifrada, $vector);
        if($rptaPlugin){
            
        //La salida esta en $arrayOut con todos los parámetros decifrados devueltos por  el VPOS
        $resultadoAutorizacion = isset($arrayOut['authorizationResult'])?$arrayOut['authorizationResult']:'-';
        $codigoAutorizacion = isset($arrayOut['authorizationCode'])?$arrayOut['authorizationCode']:'-';
        $codigoError = isset($arrayOut['errorCode'])?$arrayOut['errorCode']:'-';
        $errormensaje = isset($arrayOut['errorMessage'])?$arrayOut['errorMessage']:'-';
        Zend_Debug::dump($arrayOut);
        echo ('<br>');
        echo $resultadoAutorizacion;
        echo ('<br>');
        echo $codigoAutorizacion;
        echo ('<br>');
        echo $codigoError;
        echo ('<br>');
        echo $errormensaje;
        echo ('<br>');
        
        
        
            if(isset($arrayOut['purchaseOperationNumber'])) {
                $idOrder = $arrayOut['purchaseOperationNumber']; //COD PEDIDO
                $alignetResult = array();
                $cache = Zend_Registry::get('Cache');
                $cacheName = 'order_'.$idOrder.'_order';
                if ($result = $cache->load($cacheName)) {
                    //Exito Redireccionar         
                    $businessman = $result['businessman'];

                    if($resultadoAutorizacion=='00'){
                        $alignetResult['alignetSuccess'] = true;
                    } else {
                        $alignetResult['alignetSuccess'] = false;
                    }
                    $alignetResult['errorCode'] = $codigoError;
                    $alignetResult['authorizationResult'] = $resultadoAutorizacion;
                    $alignetResult['message'] = $errormensaje;
                    $alignetResult['authorizationCode'] = $codigoAutorizacion;
                    
                    $result['alignetResult'] = $alignetResult;
                    $cache->save($result, $cacheName);
                    $urlRedirec = 'http://'.$businessman['subdomain'].'.'
                            .$this->_config['websites'][Zend_Registry::get('domain')]
                            .'/cart/alignet-success';
                    //var_dump($result);
                    //echo $urlRedirec;
                    
                    $this->redirect($urlRedirec); 
                } else {
                    echo 'ERROR: SE PERDIÓ LA SESIÓN'; exit;
                }
           } else {
               echo 'ERROR: NO SE ENCONTRÓ NRO. DE PEDIDO'; exit;
           }
       }else{
            echo "<br> Respuesta Inv&acute;lida";
       }
    }

}

