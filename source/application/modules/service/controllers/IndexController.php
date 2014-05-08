<?php

class Service_IndexController extends Core_Controller_Action {
    
    public function init() {
        parent::init();
    }
    
    public function indexAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true); 
        echo "pasarela";
    }
    
    
    public function alignetAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true); 
        
        echo "Alignet";
        
        if(!$this->getRequest()->isPost()) $this->redirect('/');
        
        $params = $this->getAllParams();
        Zend_Debug::dump($params);
        $dataConfig=Zend_Registry::get('config');        
        $data=$dataConfig['pasarela']['alignet'];
        $vector=$data['vector'];
        $llavePrivadaCifrada = $this->selectKeyPrivadaCifrada(APPLICATION_ENV);
        $llavePublicaFirma=$this->selectKeyPublicaFirma(APPLICATION_ENV);
       
        $arrayIn['IDACQUIRER'] = $_POST['IDACQUIRER'];
        $arrayIn['IDCOMMERCE'] = $_POST['IDCOMMERCE'];
        $arrayIn['XMLRES'] = $_POST['XMLRES'];
        $arrayIn['DIGITALSIGN'] = $_POST['DIGITALSIGN'];
        $arrayIn['SESSIONKEY'] = $_POST['SESSIONKEY'];
        $arrayOut = '';        
        $pluginVPOS = new Core_Utils_VposPlugin();
        $rptaPlugin=$pluginVPOS->VPOSResponse($arrayIn, $arrayOut, $llavePublicaFirma,
            $llavePrivadaCifrada, $vector);

        $stream = @fopen(LOG_PATH.'/alignetservice.log', 'a', false);
        if (!$stream) {
            echo "Error al abrir Log.";
        }
        $writer = new Zend_Log_Writer_Stream(LOG_PATH.'/alignetservice.log');
        $logger = new Zend_Log($writer);
        
        $logger->info('***********************************');
        $logger->info('Recive Data Encrypt: '.Zend_Json_Encoder::encode($arrayIn));
        $logger->info('Recive Data: '.Zend_Json_Encoder::encode($arrayOut));
        
        if($rptaPlugin){
            //La salida esta en $arrayOut con todos los parámetros decifrados devueltos por  el VPOS
            $resultadoAutorizacion = isset($arrayOut['authorizationResult'])?$arrayOut['authorizationResult']:'-';
            $codigoAutorizacion = isset($arrayOut['authorizationCode'])?$arrayOut['authorizationCode']:'-';
            $codigoError = isset($arrayOut['errorCode'])?$arrayOut['errorCode']:'-';
            $errormensaje = isset($arrayOut['errorMessage'])?$arrayOut['errorMessage']:'-';
//            Zend_Debug::dump($arrayOut);
//            echo ('<br>');
//            echo $resultadoAutorizacion;
//            echo ('<br>');
//            echo $codigoAutorizacion;
//            echo ('<br>');
//            echo $codigoError;
//            echo ('<br>');
//            echo $errormensaje;
//            echo ('<br>');

            try {
            if(isset($arrayOut['purchaseOperationNumber'])) {
                $bizpay = $arrayOut['purchaseOperationNumber']; //COD PEDIDO
                
                if(strlen($bizpay) == 9) $bizpay = substr($bizpay, 4, 5);
                
                $mOrderDataTemp = new Shop_Model_OrderDataTemp();
                $dataOrder = $mOrderDataTemp->findByBizpay($bizpay);
                
                $idOrder = $dataOrder['idpedi'];
                $result = Zend_Json_Decoder::decode($dataOrder['data']);
                
                $logger->info('Id Orden: '.$idOrder);
                $logger->info('BizPay: '.$bizpay);
                
                $alignetResult = array();
                if (!empty($result)) {
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
                    
                    $newData = Zend_Json_Encoder::encode($result);
                    $mOrderDataTemp->updateData($idOrder, $newData);
                    if($result['module'] == "LANDING") {
                        // Landing
                        $logger->info('Modulo: Landing');
                        $urlRedirec = 'http://'.$businessman['subdomain'].'.'
                            .$this->_config['websites']['landing']
                            .'/cart/alignet-success';
                    } else {
                        // Oficina Virtual
                        $logger->info('Modulo: Oficina Virtual');
                        $urlRedirec = 'http://'.$this->_config['websites']['office']
                            .'/cart/alignet-success';
                    }

                    $logger->info('***********************************');
                    $logger->info('');
                    
                    //var_dump($result);
                    //echo $urlRedirec;
                    
                    $this->redirect($urlRedirec); 
                } else {
                    $logger->err('ERROR: No se encontro cache con la data de la orden.');
                    $logger->info('***********************************');
                    $logger->info('');
                    $this->_redirect('/');
                    //echo 'ERROR: SE PERDIÓ LA SESIÓN'; 
                    exit;
                }
           } else {
                $logger->err('ERROR: No se encontró nro de pedido.');
                $logger->info('***********************************');
                $logger->info('');
                $this->_redirect('/');
                //echo 'ERROR: NO SE ENCONTRÓ NRO. DE PEDIDO'; 
                exit;
           }
           } catch (Exception $ex) {
                $logger->err('EXCEPTION: '.$ex->getMessage().'.');
                $logger->info('***********************************');
                $logger->info('');
                $this->_redirect('/');        
           }
       }else{
            $logger->err('ERROR: Respuesta Inválida.');
            $logger->info('***********************************');
            $logger->info('');
            $this->_redirect('/');
            //echo "<br> Respuesta Inv&acute;lida";
            exit;
       }
    }
    
    public function nextPayAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true); 
        
        if(!$this->getRequest()->isPost()) $this->redirect('/');
        
        //var_dump($params);
        echo "Next Pay";
        
        $params = $this->getAllParams();
        
        $idOrder = $params['order']; // COLOCAR ID DE ORDEN REAL
        
        $stream = @fopen(LOG_PATH.'/nextpayservice.log', 'a', false);
        if (!$stream) {
            echo "Error al abrir log.";
        }
        $writer = new Zend_Log_Writer_Stream(LOG_PATH.'/nextpayservice.log');
        $logger = new Zend_Log($writer);
        $logger->info('***********************************');
        $logger->info('Id Orden: '.$idOrder);
        $logger->info('Recive Data: '.Zend_Json_Encoder::encode($params));
        
                
        $mOrderDataTemp = new Shop_Model_OrderDataTemp();
        $dataOrder = $mOrderDataTemp->findByIdOrder($idOrder);
        $result = Zend_Json_Decoder::decode($dataOrder['data']);
                
        if (!empty($result)) {
            //Exito Redireccionar         
            $businessman = $result['businessman'];
            
            //Capturar Resultado
            
            $result['nextPayResult'] = $params;
            
            $newData = Zend_Json_Encoder::encode($result);
            $mOrderDataTemp->updateData($idOrder, $newData);
            
            if($result['module'] == "LANDING") {
                // Landing
                $logger->info('Modulo: Landing');
                $urlRedirec = 'http://'.$businessman['subdomain'].'.'
                    .$this->_config['websites']['landing']
                    .'/cart/next-pay-success';
            } else {
                // Oficina Virtual
                $logger->info('Modulo: Oficina Virtual');
                $urlRedirec = 'http://'.$this->_config['websites']['office']
                    .'/cart/next-pay-success';
            }

            $logger->info('***********************************');
            $logger->info('');
            //var_dump($result);
            //echo $urlRedirec;

            $this->redirect($urlRedirec); 
        } else {
            
            $logger->err('ERROR: No se encontro cache con la data de la orden.');
            $logger->info('***********************************');
            $logger->info('');
            $this->_redirect('/');
            //echo 'ERROR: SE PERDIÓ LA SESIÓN'; 
            exit;
        }
    }
    
    public function selectKeyPrivadaCifrada($entorno)
    {
        switch ($entorno){
            case 'production' : $KeyPrivadaCifrada= "-----BEGIN RSA PRIVATE KEY-----\n".
"MIICXAIBAAKBgQDWKPqjCDjJt0QLVdZf1ZO6eLzutcXcySOXaRdfZzbd1klNUwus\n".
"5y20EqAl/2PHONUCRg3ANVDAU6L/AgkIDNAXrp4oj7fplBwVqlMfeTQ8mEmeKKCU\n".
"Fdti59t5T+qPzXlgYJ6TderDMNPMr9O7etIrtKDdrDAJ+Z0Zv9X6+VWgXwIDAQAB\n".
"AoGAAkpr6+SBnv671pczdND3BI6YDsGY/TSVkeAI5PdYqeTJ4e7aeB386iks11s0\n".
"+QONm7meGgk39OawHqQp7pEchK9Pl/TnZpGjqUEnN7vKzX7WNIDnWNsuFnVYntYJ\n".
"4k9BKAkf9A4/QMbLiA2m5r35M3ywfxyZNnNBlDLyiNtyuzkCQQD5ZFp//V5eZyS4\n".
"tIcLyTNGDsgHpWDPFCyVn5Dy3vcKrZpcczVhhab5o2LtuzoCGCgCJUn89to/EfsO\n".
"22YzyGmdAkEA29Wk+mrCVtr1wlZUIf4ane8eCDPkkUxGOzK2F2VOk4QW/IEfMX9l\n".
"PI31Mdx4YzN0lOh6FhbXLkyBd2AtEDN/KwJAF03VVxhp9RBWUhwscmF+tRE0h+Jg\n".
"RXlxIRokBU3ob8sBHZp2ZoKCs9kcqXJRXVi8hFbPrx+yDSq09xJxXm1f0QJAEY2o\n".
"o7o8lFTdhMwB4cj5dRzvx/XkzVlKzlVJRur28D5+22CwDfK/MaugJYAWOM2WBIHf\n".
"pb3jJrZDLDYIwx9piwJBAOJlDgREavJqU3huOUuM073AXbItUYD9qr7ik568h+Do\n".
"d+B8+ni5WZ9lehdk5lbrEG7PAjaE88404bqIXK5J6Zs=\n".
"-----END RSA PRIVATE KEY-----\n";
                break;
            case 'development' :  $KeyPrivadaCifrada= "-----BEGIN RSA PRIVATE KEY-----\n".
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
                break;
            default : $KeyPrivadaCifrada=false;
        }
        return $KeyPrivadaCifrada;
    }
    
    public function selectKeyPublicaFirma($entorno)
    {
        switch($entorno){
            case 'production' : $KeyPublicaFirma = "-----BEGIN PUBLIC KEY-----\n".
"MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCtvXnikeSS+H/Qs/51iL3ZYPfz\n".
"KW94WUAz7IdZIOIcuG1zLIR3kUNUc/vdSmW120dwkIleB6pl4cVT5nDewBFJCzTS\n".
"W6jGaWaryzl7xS3ZToKTHpVeQr3avN7H+Om9TfsccY7gBV3IOIauTg9xIpDjIg52\n".
"fUcfyPq+Bhw0cWkDUQIDAQAB\n".
"-----END PUBLIC KEY-----";
                break;
            case 'development' : $KeyPublicaFirma = "-----BEGIN PUBLIC KEY-----\n".
"MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCvJS8zLPeePN+fbJeIvp/jjvLW\n".
"Aedyx8UcfS1eM/a+Vv2yHTxCLy79dEIygDVE6CTKbP1eqwsxRg2Z/dI+/e14WDRs\n".
"g0QzDdjVFIuXLKJ0zIgDw6kQd1ovbqpdTn4wnnvwUCNpBASitdjpTcNTKONfXMtH\n".
"pIs4aIDXarTYJGWlyQIDAQAB\n".
"-----END PUBLIC KEY-----";
                break;            
            default : $KeyPublicaFirma = '';    
        }
        return $KeyPublicaFirma;
        
    }
}
