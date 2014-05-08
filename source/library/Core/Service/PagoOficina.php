<?php
/**
 * Coneccion de servicios con pasarela
 *
 * @author  Marcelo Carranza
 */
class Core_Service_PagoOficina implements Core_Service_SelectorPagos
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
        
        /** LOG   ***/
        $mBusinessman = new Businessman_Model_Businessman();
        $businessman = $this->_dataPago->getDataBusinessman();
        $mBusinessman->setFuxionCardLog(
               $businessman['codempr'], 
               '', 
               $this->_dataPago->getIdOrder(), 
               $this->_dataPago->getTotalOrder(), 
               '',
               'Pago Oficina'
           );
        
        
        $this->_success = true;
        $this->_mensaje = "El pago será realizado en la oficina";
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
}
