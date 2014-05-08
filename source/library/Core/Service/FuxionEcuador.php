<?php
/**
 * Coneccion de servicios con pasarela
 *
 * @author  Marcelo Carranza
 */
class Core_Service_FuxionEcuador implements Core_Service_SelectorPagos
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
        $dataConfig=Zend_Registry::get('config');
        $this->_dataConfig=$dataConfig;
        $this->_dataPago=$dataPago;         
        
        $urlSend = $dataConfig['pasarela']['fuxionecuador']['url'];
        
          /** LOG   ***/
        $mBusinessman = new Businessman_Model_Businessman();
        $businessman = $this->_dataPago->getDataBusinessman();
        $mBusinessman->setFuxionCardLog(
               $businessman['codempr'], 
               '', 
               $this->_dataPago->getIdOrder(), 
               $this->_dataPago->getTotalOrder(), 
               '',
               'Ecuador'
           );
        
        $html = $this->setFormHTML($urlSend, array());
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
    
    public function setFormHTML($url, $sendData)
    {        
 
        $html='<form name="frmSolicitudPago" method="get" 
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
}
