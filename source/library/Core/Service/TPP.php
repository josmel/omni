<?php
/**
 * Coneccion de servicios con pasarela
 *
 * @author  Marcelo Carranza
 */
class Core_Service_TPP implements Core_Service_SelectorPagos
{
    
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
    private $_mensaje = "";
    
    private $_success = false;
        
    public function __construct($dataPago,$dataConfig)
    {
        $this->_dataPago=$dataPago;   
        $this->_dataConfig=$dataConfig;
    }
    
    
    
    
    public function getMensaje()
    {
        return $this->_mensaje;
    }
    
    public function isSuccess()
    {
        return $this->_success;
    }
    
    public function pagar()
    {
        $this->sendData();
        
    }
    
    public function sendData()
    {
        $dataConfig = Zend_Registry::get('config');
        $mBusinessman = new Businessman_Model_Businessman();
        
        /** LOG **/
        $businessman = $this->_dataPago->getDataBusinessman();
        $mBusinessman->setFuxionCardLog(
                $businessman['codempr'], 
                $this->_dataPago->getCardCode(), 
                $this->_dataPago->getIdOrder(), 
                $this->_dataPago->getTotalOrder(), 
                $this->_dataPago->getDollarFactor(),
                'Tarjetas Fuxion'
            );
        
//        if ($mBusinessman->hasFuxionCard($this->_dataPago->getCardCode())) {
//            $this->_mensaje = 'El número de tarjeta es inválido.';
//            $this->_success = false;   
//            return;
//        } else {
//            $mBusinessman->setFuxionCardLog(
//                $businessman['codempr'], 
//                $this->_dataPago->getCardCode(), 
//                $this->_dataPago->getIdOrder(), 
//                $this->_dataPago->getTotalOrder(), 
//                $this->_dataPago->getDollarFactor(),
//                'Tarjetas Fuxion'
//            );
//            
//            $this->_mensaje = 'La operación se realizó con éxito.';
//            $this->_success = true;   
//        }
//        
//        return;
        
        $sendData = array(
            'url' => $dataConfig['pasarela']['tpp']['url'],
            'CodeTransaction' => $dataConfig['pasarela']['tpp']['CodeTransaction'],
            'CodeOperation' => $dataConfig['pasarela']['tpp']['CodeOperation'],
            'CodeProduct' => $dataConfig['pasarela']['tpp']['CodeProduct'],
            'CodeMerchant' => $dataConfig['pasarela']['tpp']['CodeMerchant'],
            'key' => $dataConfig['pasarela']['tpp']['key'],
            'NewKey' => $dataConfig['pasarela']['tpp']['NewKey']
        );
        //var_dump($sendData);
        if (isset($dataConfig['testData'])) {
            $sendData['testData'] = $dataConfig['testData']['urlPayment'];
        }        
        $url = $sendData['url'];
        
        if(!empty($sendData['testData'])){
            $url.='?'.$sendData['testData'];
        } else {
            $numberPhone = $this->_dataPago->getCardCode();
            $total = $this->_dataPago->getTotalOrder();
            $shipPrice = $this->_dataPago->getShipPrice();
            
            $url.='?NumberPhone='.$numberPhone
                  .'&CodeTransaction='.$sendData['CodeTransaction']
                  .'&CodeOperation='.$sendData['CodeOperation']
                  .'&CodeProduct='.$sendData['CodeProduct']
                  .'&CodeMerchant='.$sendData['CodeMerchant']
                  .'&Amount='.number_format($total, 2, '.', '')
                  .'&key'
                  .'&NewKey';      
        }
        
        $stream = @fopen(LOG_PATH.'/tppservice.log', 'a', false);
        if (!$stream) {
            echo "Error al abrir log.";
        }
        $writer = new Zend_Log_Writer_Stream(LOG_PATH.'/tppservice.log');
        $logger = new Zend_Log($writer);
        $logger->info('***********************************');
        $logger->info('Id Orden: '.$this->_dataPago->getIdOrder());
        $logger->info('Url Send: '.$url);
        
        
        //echo $url; 
        //echo "<br />";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url); 
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, '3');
        $content = trim(curl_exec($curl));
        curl_close($curl);
        try {
            $content = new SimpleXMLElement($content);
            //var_dump($content);
            $codeResponse = $content->tpTransactionMagicaResult->CodeResponse;
            $logger->info('Result: '.Zend_Json_Encoder::encode($content));
//            Zend_Debug::dump($content);exit;
            $this->_mensaje = (string) $content->tpTransactionMagicaResult->MessageResponse;
        } catch (Exception $ex) {
            $codeResponse = '98';
            echo  $this->_mensaje = 'Ocurrió un error de conexión.';
            $logger->err('ERROR: Error al leer respuesta.');
        }
        
        $logger->info('***********************************');
        $logger->info('');
        
        if($codeResponse == '00'){
            $this->_success = true;
        }else{ 
            $this->_success = false;    
        }
                               
    }
        
}

