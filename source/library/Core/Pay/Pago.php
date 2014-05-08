<?php
/**
 * Pattern strategy
 *
 * @author marrselo
 */
class Core_Pay_Pago
{
    const TARJETA_CREDITO ="TP004";
    const TARJETA_FUXION='TP011';
    const PAGO_ONLINE ="TP002323";
    const PAGO_OFICINA ="TP005";
    const DEPOSITO_BANCO ="TP006";
    
    /*
     * Respuesta final de la operacion;
     * @var bool $_succes;
     */
    private $_succes;    
    /*
     * Selector del medio de pago
     * @var string $_selectorPagos;
     */
    private $_selectorPagos;
    /*
     * Mensaje de los medios de pago
     * @var string $_mensaje
     */
    
    /**
     * __Construct         
     *
     * @param  array $dataTipoPublicacion
     * @param  string $medioPago ;  'pasarela' / 'pagoefectivo'     
     */
    public function __construct($medioPago,$dataPago,$dataConfig, $country = '604') {        
        $this->seleccionarMedioPago($medioPago,$dataPago,$dataConfig, $country);
    }
        
    public function seleccionarMedioPago($medioPago,$dataPago,$dataConfig,$country)
    {
        switch($medioPago){           
            case self::TARJETA_FUXION:                
                $this->_selectorPagos = new Core_Service_TPP($dataPago,$dataConfig['tpp']);
                break;
            case self::TARJETA_CREDITO:
                switch($country) {
                    case '604': $this->_selectorPagos = new Core_Service_Alignet($dataPago,$dataConfig['alignet']); break;
                    case '068': $this->_selectorPagos = new Core_Service_Alignet($dataPago,$dataConfig['alignet']); break;
                    case '152': $this->_selectorPagos = new Core_Service_Alignet($dataPago,$dataConfig['alignet']); break;
                    case '484': $this->_selectorPagos = new Core_Service_Alignet($dataPago,$dataConfig['alignet']); break;
                    case '591': $this->_selectorPagos = new Core_Service_Alignet($dataPago,$dataConfig['alignet']); break;
                    case '840': $this->_selectorPagos = new Core_Service_Alignet($dataPago,$dataConfig['alignet']); break;
                    case '862': $this->_selectorPagos = new Core_Service_Alignet($dataPago,$dataConfig['alignet']); break;
                    case '188': $this->_selectorPagos = new Core_Service_NextPay($dataPago,$dataConfig['nextpay']); break;
                   // case 218: $this->_selectorPagos = new Core_Service_NextPay($dataPago,$dataConfig['nextpay']); break;
                    case '218': $this->_selectorPagos = new Core_Service_FuxionEcuador($dataPago,$dataConfig['fuxionecuador']); break;
                    case '591': $this->_selectorPagos = new Core_Service_NextPay($dataPago,$dataConfig['nextpay']); break;
                    case '170': $this->_selectorPagos = new Core_Service_PagosOnline($dataPago,$dataConfig['pagosonline']); break;
                }
                break;
            case self::PAGO_ONLINE:
                switch($country) {
                }
                break;
            
            case self::PAGO_OFICINA:
                $this->_selectorPagos = new Core_Service_PagoOficina($dataPago,array()); break;
                break;
            
            case self::DEPOSITO_BANCO:
                $this->_selectorPagos = new Core_Service_DepositoBancario($dataPago,array()); break;
                break;
            default :
        }        
    }
    
    public function pagar()
    {
       return $this->_selectorPagos->pagar();        
    }
    /**
     * 
     * @return string mensaje final despues del proceso de pago
     */
    public function getMensaje()
    {
        return $this->_selectorPagos->getMensaje();
    }
    
    public function isSuccess()
    {
        return $this->_selectorPagos->isSuccess();
    }

}
