<?php

class Shop_Form_Payment extends Core_Form_Form{
    /**
     *
     * @var type array
     */
    protected $_businessman = array();
    protected $_subTotal = 0;
    protected $_igvTotal = 0;
    protected $_iva = 0;
    protected $_shipPrice = 0;
    protected $_total = 0;
    protected $_isLanding = false;
    
    public function __construct($businessman, $subTotal, $igvTotal, $iva, $shipPrice, $total, $isLanding = true, $options = null) {
        $this->_businessman = $businessman;
        $this->_subTotal = $subTotal;
        $this->_igvTotal = $igvTotal;
        $this->_iva = $iva;
        $this->_shipPrice = $shipPrice;
        $this->_total = $total;
        $this->_isLanding = $isLanding;
        
        parent::__construct($options);
    }
    
    public function init() {                        
        $mPayMethod = new Shop_Model_PayMethod();        
        
        $e = new Zend_Form_Element_Select('paymethod');
        $e->setAttrib('class', 'select-large');
        $dataPayMethod = $mPayMethod->findPayMethodByCountryPairs(
            $this->_businessman['codpais'], $this->_isLanding); 
        $e->setMultiOptions($dataPayMethod);
        $this->addElement($e);
        
        $e = new Zend_Form_Element_Text('cardcode');
        $e->setAttrib('class', 'inpt-xmedium numeric');
        $e->setAttrib('placeholder', 'Ingrese el código de la tarjeta');
        $this->addElement($e);  
        
        $e = new Zend_Form_Element_Text('transcode');
        $e->setAttrib('class', 'inpt-xmedium numeric');
        $e->setAttrib('placeholder', 'Ingrese el Nro de Transacción');
        $this->addElement($e);  
        
        foreach($this->getElements() as $element) {
            $element->removeDecorator('Label');
            $element->removeDecorator('DtDdWrapper');          
            $element->removeDecorator('HtmlTag');
        }
    }
    
    public function getBusinessman() {
        return $this->_businessman;
    }
    
    public function getSubTotal() {
        return $this->_subTotal;
    }
    
    public function getIgvTotal() {
        return $this->_igvTotal;
    }
    
    public function getIva() {
        return $this->_iva;
    }
    
    public function getShipPrice() {
        return $this->_shipPrice;
    }
    
    public function getTotal() {
        return $this->_total;
    }
}