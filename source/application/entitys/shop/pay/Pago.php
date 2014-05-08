<?php
/**
 * Pattern strategy
 *
 * @author marrselo
 */
class Shop_Pay_Pago
{

    const TARJETA_ALIGNET ="TP004";
    const TARJETA_FUXION='TP011';
    
    
    
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
    public function __construct($medioPago,$dataPago) {        
        $this->seleccionarMedioPago($medioPago,$dataPago);
    }
        
    public function seleccionarMedioPago($medioPago,$dataPago)
    {
        switch($medioPago){           
            case self::TARJETA_FUXION:
                $this->_selectorPagos = new Core_Service_TPP($dataPago);
                break;
            case self::TARJETA_ALIGNET:
                $this->_selectorPagos = new Core_Service_Alignet($dataPago);
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
