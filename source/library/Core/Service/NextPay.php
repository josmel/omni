<?php
/**
 * Coneccion de servicios con pasarela
 *
 * @author  Marcelo Carranza
 */
class Core_Service_NextPay implements Core_Service_SelectorPagos
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
    
    const DECLINED = 'declined';
    const APPROVED = 'approved';
    const PROCESSING = 'processing';
    const FAILED = 'failed';
    
    private static $_errorState = array(
        '2' => self::DECLINED, 
        '10' => self::APPROVED, 
        '11' => self::PROCESSING, 
        '12' => self::FAILED 
    );
            
    public function __construct($dataPago,$dataConfig)
    {
        $dataConfig=Zend_Registry::get('config');
        $this->_dataConfig=$dataConfig;
        $this->_dataPago=$dataPago;         
        
        $telephone =  '';
        $sendAddress = '';
        $businessman = $dataPago->getDataBusinessman();
        
        if ($dataPago->getDataJoined() != null) {
            $mAddress = new Businessman_Model_ShipAddress();
            $address = $mAddress->findByIdExtend($dataPago->getIdAddress());
            
            $email = $dataPago->getDataJoined()->email;
            $firstName = $dataPago->getDataJoined()->name;
            $lastName = $dataPago->getDataJoined()->lastname;
            $sendAddress = $dataPago->getIdAddress() == -1 ? 'SHOP' : $address['destvia'].' '.$address['ubidenv'];
        } else {
            $mAddress = new Businessman_Model_Address();
            $address = $mAddress->findByIdExtend($dataPago->getIdAddress());
            
            $email = $businessman['emaempr'];
            $firstName = $businessman['nomempr'];
            $lastName = $businessman['appempr'].' '.$businessman['apmempr'];
            $telephone = $businessman['telefono'];
            $sendAddress = $dataPago->getIdAddress() == -1 ? 'SHOP' : $address['destvia'].' '.$address['desdire'].' '.$address['numdire'].' Int. '.$address['intdire'];
        }
        
        if($dataPago->getIdAddress() == -1) {
            $clientCity = $dataConfig['pasarela']['nextpay']['defaultCity'];
            $clientState = $dataConfig['pasarela']['nextpay']['defaultState'];
        } else {
            $clientCity = $address['nameubig2'];
            $clientState = $address['nameubig1'];
        }
        
        /** LOG   ***/
        $mBusinessman = new Businessman_Model_Businessman();
        $mBusinessman->setFuxionCardLog(
               $businessman['codempr'], 
               '', 
               $this->_dataPago->getIdOrder(), 
               $this->_dataPago->getTotalOrder(), 
               '',
               'Next Pay'
           );
        $sendData = array(
            'client_name' => $firstName,
            'client_lastname' => $lastName,
            'client_address' => $sendAddress,
            'client_city' => $clientCity,
            'client_state' => $clientState,
            'client_country' => $businessman['nompais'],
            'client_postcode' => $dataConfig['pasarela']['nextpay']['postcode'],
            'client_phone' => empty($telephone) ? $dataConfig['pasarela']['nextpay']['clientPhone'] : $telephone,
            'client_email' => empty($email) ? $dataConfig['pasarela']['nextpay']['defaultEMail'] : $email,
            'id_commerce' => $dataConfig['pasarela']['nextpay']['idCommerce'],
            'order' => $dataPago->getIdOrder(),
            'amount' => number_format($dataPago->getTotalOrder('DOLLAR'), 2, '.', ''),
            'urlreturn' => SITE_URL.$dataConfig['pasarela']['nextpay']['urlReturn'],
            'currency' => $dataConfig['pasarela']['nextpay']['currencyCode']
        );
        
        $stream = @fopen(LOG_PATH.'/nextpayservice.log', 'a', false);
        if (!$stream) {
            echo "Error al abrir log.";
        }
        $writer = new Zend_Log_Writer_Stream(LOG_PATH.'/nextpayservice.log');
        $logger = new Zend_Log($writer);
        $logger->info('***********************************');
        $logger->info('Id Orden: '.$dataPago->getIdOrder());
        $logger->info('Send Data: '.Zend_Json_Encoder::encode($sendData));
        $logger->info('***********************************');
        $logger->info('');
        
        //var_dump($sendData); exit;
        $urlSend = $dataConfig['pasarela']['nextpay']['url'];
        $html = $this->setFormHTML($urlSend, $sendData);
        echo $html; 
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
    
    public function setFormHTML($url, $sendData)
    {        
 
        $html='<form name="frmSolicitudPago" method="post" 
        action="'.$url.'" id="frmSolicitudPago">';
        foreach ($sendData as $key => $value) {
            $html.= '<input type="hidden" name="'.htmlentities($key).'" value="'.htmlentities($value).'">';
        }
        $html.= '
        </form>
        <script language="JavaScript">
        document.frmSolicitudPago.submit();
        </script>';
        
        return $html;
    }
    
    public static function getState($code) {   
        return isset(self::$_errorState[$code]) ? self::$_errorState[$code] : 'unknow';
    }
    
    public static function getMessage($state) {     
        $msg = '';
        
        switch ($state) {
            case self::APPROVED: $msg = "Su pago se realizó con éxito."; break; 
            case self::DECLINED: $msg = "No se realizó el pago."; break; 
            case self::PROCESSING: $msg = "El proceso de pago fue interrumpido."; break; 
            case self::FAILED: $msg = "Su pago no se realizó correctamente."; break; 
            default: $msg = "Error desconocido"; break;
        }
        return $msg;
    }
}
