<?php
/**
 * Coneccion de servicios con pasarela
 *
 * @author  Marcelo Carranza
 */
class Core_Service_Alignet implements Core_Service_SelectorPagos
{
    const COD_MON='840';    
    
    /**
     * datos de configuración de pasarela
     * @var array $_dataConfig
     */
    private $_dataConfig;
    /*
     * datos del pago
     * @var array $_dataPago
     */
    private $_dataPago;
    /*
     * @var string $_mensaje 
     */
    private $_mensaje;
    
    private $_success;
    
        
    public function __construct($dataPago,$dataConfig)
    {
        $this->_dataConfig=$dataConfig;
        $this->_dataPago=$dataPago;         
        $dataPasarela=$this->_dataConfig;
        $arrayIn = $this->arrayIn($dataPago, $dataPasarela);
        $arrayOut = $this->arrayOut(); 
        $llavePublicaCifrado=$dataPasarela['keypublic'];
        $llavePrivadaFirma=$dataPasarela['keyprivate'];
        $vector = $dataPasarela["vector"];
        $entorno=APPLICATION_ENV;
        $llavePublicaCifrado =$this->selectKeyPublicaCifrada($entorno);  
        $llavePrivadaFirma = $this->selectKeyPrivadaFirma($entorno);
                
        $stream = @fopen(LOG_PATH.'/alignetservice.log', 'a', false);
        if (!$stream) {
            echo "Error al guardar.";
        }
        $writer = new Zend_Log_Writer_Stream(LOG_PATH.'/alignetservice.log');
        $logger = new Zend_Log($writer);
        $logger->info('***********************************');
        $logger->info('Id Orden: '.$dataPago->getIdOrder());
        $logger->info('BizPay: '.$dataPago->getBizpay());
        $logger->info('Data: '.Zend_Json_Encoder::encode($arrayIn));
            
        $this->_success = true;
        $pluginVPOS = new Core_Utils_VposPlugin();
        $arraySend = array();
        $arrayResponse = array();        
        $stateSend = $pluginVPOS->VPOSSend($arrayIn, $arraySend, $llavePublicaCifrado, 
            $llavePrivadaFirma,$vector);        
        
        $logger->info('Encrypt Data Send: '.Zend_Json_Encoder::encode($arraySend));
        $logger->info('***********************************');
        $logger->info('');
         /** LOG   ***/
        $mBusinessman = new Businessman_Model_Businessman();
        $businessman = $this->_dataPago->getDataBusinessman();
        $mBusinessman->setFuxionCardLog(
               $businessman['codempr'], 
               $this->_dataPago->getBizpay(), 
               $this->_dataPago->getIdOrder(), 
               $this->_dataPago->getTotalOrder(), 
               '',
               'Alignet'
           );
        $this->_mensaje= $this->setFormHTML($arraySend,true);
              
    }
           
    public function getMensaje()
    {
        return $this->_mensaje;
    }
    
    public function isSuccess()
    {
        return $this->_success;
    }
    
    public function getDataVPOS()
    {
        
    }
    
    public function pagar()
    {
        
    }
    
    public function arrayIn($dataPago,$dataPasarela)
    {
        $iva = $dataPago->getIva();
        $total = $dataPago->getTotalOrder('DOLLAR');
        $shipPrice = $dataPago->getShipPrice();
        $bizPay = $dataPago->getBizpay();
        
//        $cache = Zend_Registry::get('Cache');
//        $dataCache = array('idOrder' => $dataPago->getIdOrder());
//        $cache->save($dataCache, 'bizpay_'.$bizPay.'_order');
        $businessman = $dataPago->getDataBusinessman();
        
        if ($dataPago->getDataJoined() != null) {
            $email = $dataPago->getDataJoined()->email;
            $firstName = $dataPago->getDataJoined()->idcliper.'('.$businessman['codempr'].')';
            //$firstName = $dataPago->getDataJoined()->name;
            $lastName = $dataPago->getDataJoined()->name.' '.$dataPago->getDataJoined()->lastname;
        } else {
            
            $email = $businessman['emaempr'];
            //$firstName = $businessman['nomempr'];
            $firstName = $businessman['codempr'];
            $lastName = $businessman['nomempr'].' '.$businessman['appempr'].' '.$businessman['apmempr'];
        }
        
        if(empty($email)) $email = "envios@prolife.com.pe";
        
        $array_send=array();
        $mount = number_format($total, 2);
        $mountArray=explode('.',$mount);

        $array_send['acquirerId']=$dataPasarela['idacquirer']; 
        $array_send['commerceId']=$dataPasarela['idcommerce'];
        $array_send['purchaseAmount']=$mountArray[0].$mountArray[1]; 
        $array_send['purchaseCurrencyCode']=  self::COD_MON; 
        $array_send['purchaseOperationNumber']= $bizPay;
        $array_send['billingAddress']='San Isidro Juan Elespuru 305 Lima'; 
        $array_send['billingCity']='Lima'; 
        $array_send['billingState']='LI'; 
        $array_send['billingCountry']='PE'; 
        $array_send['billingZIP']='Lima031'; 
        $array_send['billingPhone']='988923123'; 
        $array_send['billingEMail']=$email; 
        $array_send['billingFirstName']=$firstName; 
        $array_send['billingLastName']=$dataPago->getIdOrder(); 
        $array_send['language']='SP';
        $array_send['reserved1']=self::COD_MON;
        $array_send['reserved2']=$mountArray[0].$mountArray[1];;
        $array_send['reserved3']=$dataPasarela['idcommerce'];
        //En español
//        var_dump($array_send); exit;
        return $array_send;
    }
    
    public function arrayOut()
    {
        $array_get = array();
        $array_get['XMLREQ']=""; 
        $array_get['DIGITALSIGN']=""; 
        $array_get['SESSIONKEY']="";
        
        return $array_get;
    }
    
    public function setStyle()
    {
        $style="<style> .imgloadvpos {
                    position: absolute;top: 17.5%;left: 19%;
                    width: 595px;
                    height: 485px;
                    z-index: 1003;background-color: #FFFFFF;
                    visibility: hidden;border-width: 1px;border-style: ridge;
                    }
                  .overlayvpos {
                    position: absolute;
                    top: 0;left: 0;background: #000;z-index: 1001;
                    opacity: 0.30;filter: alpha(opacity=30); visibility: hidden; 
                    height: 1024px;
                    width: 800px;
                    }
                    .modalvpos {
                    position: absolute;top: 17.5%;left: 19%;width: 595px;height: 485px;
                    padding: 0px;z-index: 1004;border-width: 1px;border-style: ridge;visibility: hidden;margin: 0px;
                    }
                    .iframevpos {
                        padding: 0px; clear: both; width: 595px; height: 485px; margin: 0px;border: 0px;
                    }
                    imgloadingvpos {
                    position: absolute;
                    left:46%;
                    top: 42%;
                    }
                    </style>";
    }
    public function setFormHTML($paramsPOST,$onload=false)
    {
        $submit='';
        if($onload){ 
            $submit='<body onload="document.getElementById('."'Alignet'". ').submit();">';            
        }
        $html='<Html>
	<head>'.$this->setStyle().' '.$submit
        .'
	<title>Pagina prueba Visa</title>
	</head>
        <img src="/static/img/loading.gif" >
	<form name="frmSolicitudPago" method="post" 
        action="'.$this->_dataConfig['url'].'" id="Alignet">
        <input type="hidden" name="IDACQUIRER" value="'.$this->_dataConfig['idacquirer'].'">
        <input type="hidden" name="IDCOMMERCE" value="'.$this->_dataConfig['idcommerce'].'">
        <input type="hidden" name="XMLREQ" value="'.$paramsPOST['XMLREQ'].'">
        <input type="hidden" name="DIGITALSIGN" value="'.$paramsPOST['DIGITALSIGN'].'">
        <input type="hidden" name="SESSIONKEY" value="'.$paramsPOST['SESSIONKEY'].'">      
        </form>
	</Body>
	</Html>';
        return $html;
    }
    
    public function selectKeyPublicaCifrada($entorno)
    {
        switch ($entorno){
            case 'production' : $keyPublicCifrada= "-----BEGIN PUBLIC KEY-----\n".
"MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC0t0Cnbne8gQoeGK4nG6O3zfwh\n".
"q8u9Wp5zHjyVYbvx2zudSOlBnJ5qU74BcTGypbn6W7jjvSNE7AmncOAVh4RxuRXO\n".
"+bINFIyQ7/ErH/v1YpDFk8knC/NuvFpfHqhJ/5j2I8y+WmyF0MZmGtm074nUGv4d\n".
"qlbUMT9aYUQ+RzMO7QIDAQAB\n".
"-----END PUBLIC KEY-----\n";
                break;
            case 'development' :  $keyPublicCifrada= "-----BEGIN PUBLIC KEY-----\n".
"MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDTJt+hUZiShEKFfs7DShsXCkoq\n".
"TEjv0SFkTM04qHyHFU90Da8Ep1F0gI2SFpCkLmQtsXKOrLrQTF0100dL/gDQlLt0\n".
"Ut8kM/PRLEM5thMPqtPq6G1GTjqmcsPzUUL18+tYwN3xFi4XBog4Hdv0ml1SRkVO\n".
"DRr1jPeilfsiFwiO8wIDAQAB\n".
"-----END PUBLIC KEY-----";
                break;
            default : $keyPublicCifrada=false;
        }
        return $keyPublicCifrada;
    }
    
    public function selectKeyPrivadaFirma($entorno)
    {
        switch($entorno){
            case 'production' : $llavePrivadaFirma = "-----BEGIN RSA PRIVATE KEY-----\n".
"MIICXQIBAAKBgQCvA5JKE7E7aTsbSjU3BL3XyfcVqJnLUdYaGTTzGRr5cJNVS8IG\n".
"KVNC7ElFrowMqIhEkgH+4xAtCWnnUi1CzGQ+Ab7gwskztQjFp9wFOf8QeEn6ABFI\n".
"zp+qhUCvgQHdt6vlh1tCzURuScoW2HdT1ooNbBd1RKpmGK0vy04p0ZbY8QIDAQAB\n".
"AoGAKEB5B+KJk7F7L6acoJ/NjfDcjDWv0Yxr4qrDe9ndU1tp92cqI8KjbLPtkkI3\n".
"4b1tQeAW52mP0dvlaJeE42Ug9KR4FMnguV06EFZDtt7vgs0OXJBFL5tdjjEWKpvF\n".
"Hzf8/IQVLUO19J0m5OUgQ04WdKRy3KS15lEbncqwySCtl/ECQQDdpL4YXI3gfKeW\n".
"NnaUxb9rE695QJzz/T3f84DDgbFb75kcfGHZ87uZJ3E3JgLxamgYbZSK7LUP3TwT\n".
"hGtf0jQ9AkEAyiR5a7XyWBTLkm+DBG4FwHm9t+uPH+0ttYPgU3219f9x2VtZQAc8\n".
"lNLqLWHRL6//XFV3OmcSlygMv0SpCWOexQJBAIDqvKLg2qjy+GsXnJtl9bOrTIoj\n".
"Oed0qdOkB4Yv3mBSGWWHN0cVTE2FsoVYN6gBszBaNGclzR7AGJxljiq60iUCQQCO\n".
"4Cz6VsYOP9SLkkn2rZVfMC1KfDLTjB7Mt4OtP6OHVqAv04NH4FWPk2x12zeyDyHd\n".
"L4NwQj4/nM01u+AGhOVJAkAdpgeJg2iV23glZ0GaajBmKKVTTM/zcqnzQ3vz5gTh\n".
"01cJiG8PxsT7LHpar5nGlh8rmhYIGcubyFUTVVajVPwU\n".
"-----END RSA PRIVATE KEY-----";
                break;
            case 'development' : $llavePrivadaFirma = "-----BEGIN RSA PRIVATE KEY-----\n".
"MIICXQIBAAKBgQC0FWC3Hv8CPW2XKAd6McXIcwX/sKxKjcmtqJi271ELqD4iWh1L\n".
"2pUPNqlD4QNLEHs12c92BZ3lzbB2tw16DExsJ/Q9OEIrrKY3Ur3WnqM/7Fw5Bftp\n".
"lArqe4WHOxcT1vzuLU3JzVJUxzeGgmpyMIILbPHvvJiFVuel/4t3x/uRtwIDAQAB\n".
"AoGALZNDeNTFYQG+3IRq8AfssEA8AmvqDqw3oFWM4K9MaZDYuMTpqSrWkpUY3W7y\n".
"8GppZEWNdacSQPh/cmv9Yyf9puz2M4Pfvwd5ZKQ6O0Z33RkIIAsQzrWijchfN7oL\n".
"mfWRLpxvlgHKB25Uno7KYMHhSCw58HepuU1lbgjw2M9IhAECQQDcYQCONsPxaPcF\n".
"59pYjRgvlDw215Z/6ZAANtGbxZ/Sh0kNxbFImijJIcWvIbegKq1f1sS/Qp3WdCIm\n".
"zmKx7ncBAkEA0TEAzqrFH5bVGLcAD1ZmsnuZm3t3W9nuPT6YF3jne1nUNHVCM6fi\n".
"hI0kczQ9C3qOxU1Z0xpvVzqxd0UwfwSAtwJBAKxGqEiER2jQxUq4lNfkdKO9HdVB\n".
"c0VLMGb6c6FyPB4348QAZzZ6TKfu3rqpgQKUWjQxkFxzly+uWYQ8kqrF4AECQAUB\n".
"RyOqskyvLyoINtgnGQ3sJyCxM4nfDPQqKzlkiyVIYOtgYtUZjzHcrqKS/WLo68GM\n".
"uDl0yHUt0lEoNqDTWsECQQCWEticCPidOEcR6NFJgvJtmmkVqgyaH52R1+tEg816\n".
"Aj61sSVRzC8ioUOESjKXvQ+ITq3Xm7bb7QIUZNRkjKWJ\n".
"-----END RSA PRIVATE KEY-----\n";
                break;            
            default : $llavePrivadaFirma = '';    
        }
        return $llavePrivadaFirma;
        
    }

}
