<?php

class Businessman_Form_Report extends Zend_Form {
    
     protected $_idEmpr = '';

    public function __construct($id = null, $options = null) {
        $this->_idEmpr = $id;
        parent::__construct($options);
    }

    
    
    public function init() {
        $seoFilter=new Core_Utils_SeoUrl();
        $this->setMethod('post');      
        $this->setEnctype('multipart/form-data');
        $e = new Zend_Form_Element_Select('tpais');
        $objType = new Businessman_Model_Report();
        $e->setMultiOptions($objType->getCountryAll());
        $this->addElement($e);
        $e = new Zend_Form_Element_Select('tsemana');
        $codsema= $objType->getAffiliate($this->_idEmpr);
        $idf= $codsema['codsema'];
        $e->setMultiOptions($objType->getWeekAll($idf));
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
        
}