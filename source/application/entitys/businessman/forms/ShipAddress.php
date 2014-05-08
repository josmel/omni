<?php

class Businessman_Form_ShipAddress extends Zend_Form {
    /**
     *
     * @var type array
     */
    protected $_dataCaptcha=array();
    
//    
//    public function __construct($dataCaptcha) {
//        $this->_dataCaptcha=$dataCaptcha;
//    }
    
    public function init() {       
        $seoFilter=new Core_Utils_SeoUrl();
        
        $obj=new Application_Model_DbTable_ShipAddress();
        $primaryKey=$obj->getPrimaryKey();
        
        $this->setMethod('post');      
        $this->setEnctype('multipart/form-data');
        $this->setAttrib('iddenv',$primaryKey);
        
        $e = new Zend_Form_Element_Hidden($primaryKey);                         
        $this->addElement($e);
        
        $e = new Zend_Form_Element_Text('ubidenv'); 
        $e->setAttrib('class', 'inpt-xsmall');
        $e->setAttrib('placeholder', 'Dirección');
        $e->setRequired(true);
        $this->addElement($e);
        
        $e = new Zend_Form_Element_Text('refdenvio'); 
        $e->setAttrib('class', 'inpt-large');
        $e->setAttrib('placeholder', 'Referencia');
        $this->addElement($e);     
        
        $mStreetType = new Businessman_Model_StreetType();
        $e = new Zend_Form_Element_Select('codtvia');  
        $e->setAttrib('class', 'select-small');
        $e->setRequired(true);
        $e->setMultiOptions($mStreetType->getAllPairs());
        $this->addElement($e); 
        
        $e = new Zend_Form_Element_Select('country'); 
        $e->setAttrib('class', 'select-medium');
        $e->setRequired(true);
        $this->addElement($e);
        
        $e = new Zend_Form_Element_Select('state');    
        $e->setAttrib('class', 'select-medium');
        $e->setRequired(true);
        $e->setMultiOptions(array("" => "-Departamento-"));
        $this->addElement($e);     
        
        $e = new Zend_Form_Element_Select('district');
        $e->setAttrib('class', 'select-medium');
        $e->setRequired(true);
        $e->setMultiOptions(array("" => "-Distrito-"));
        $this->addElement($e);     
        
        $e = new Zend_Form_Element_Select('city');    
        $e->setAttrib('class', 'select-medium');
        $e->setRequired(true);
        $e->setMultiOptions(array("" => "-Provincia-"));
        $this->addElement($e);     
        
        foreach($this->getElements() as $element) {
            $element->removeDecorator('Label');
            $element->removeDecorator('DtDdWrapper');          
            $element->removeDecorator('HtmlTag');
        }
    }
    
    public function isValid($data) {
        $isValid = parent::isValid($data);
        
        return $isValid;
    }
    
    public function setCountry($idCountry, $countryName) {        
        $this->getElement('country')->setMultiOptions(array($idCountry => $countryName));
        
        $mUbigeo = new Businessman_Model_Ubigeo();
        $this->getElement('state')->setMultiOptions($mUbigeo->findAllByCountryPairs($idCountry, "", "-Departamento-"));
    }
}