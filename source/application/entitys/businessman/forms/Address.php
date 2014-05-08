<?php

class Businessman_Form_Address extends Zend_Form {
    /**
     *
     * @var type array
     */
    protected $_dataCaptcha=array();
    protected $_ubigeoTree = array();
    protected $_idCountry = '';
    protected $_nameCountry = '';
    
    public function getUbigeoTree() {
        return $this->_ubigeoTree;
    }
    
    public function __construct($idCountry, $nameCountry, $ubigeoTree, $options = null) {
        $this->_idCountry = $idCountry;
        $this->_nameCountry = $nameCountry;
        $this->_ubigeoTree = $ubigeoTree;
        parent::__construct($options);
    }
//    
//    public function __construct($dataCaptcha) {
//        $this->_dataCaptcha=$dataCaptcha;
//    }
    
    public function init() {       
        $seoFilter=new Core_Utils_SeoUrl();
        
        $obj=new Application_Model_DbTable_Address();
        $primaryKey=$obj->getPrimaryKey();
        
        $this->setMethod('post');      
        $this->setEnctype('multipart/form-data');
        $this->setAttrib('iddenv',$primaryKey);
        
        $e = new Zend_Form_Element_Hidden($primaryKey);                         
        $this->addElement($e);
        
        $e = new Zend_Form_Element_Text('desdire');
        $e->setRequired(true);
        $this->addElement($e);
        
        $e = new Zend_Form_Element_Text('refdire');
        $this->addElement($e);     
        
        $e = new Zend_Form_Element_Text('telefono');
        $this->addElement($e);     
        
        $e = new Zend_Form_Element_Text('zipcode');
        $this->addElement($e);  
        
        $mStreetType = new Businessman_Model_StreetType();
        $e = new Zend_Form_Element_Select('codtvia');
        //$e->setRequired(true);
        //$e->setMultiOptions($mStreetType->getAllPairs('abbrtvia'));
        $this->addElement($e); 
        
        $e = new Zend_Form_Element_Text('numdire');
        $e->setAttrib('class', 'txtSmall');
        //$e->setRequired(true);
        $this->addElement($e); 
        
        $e = new Zend_Form_Element_Text('intdire');
        $e->setAttrib('class', 'txtSmall');
        $this->addElement($e);
        
        $e = new Zend_Form_Element_Text('nomcont');
        $this->addElement($e);     
        
        $e = new Zend_Form_Element_Text('country'); 
        $e->setAttrib('class', 'select-medium');
        $e->setAttrib('readonly', 'readonly');
        $e->setValue($this->_nameCountry);
        $e->setRequired(true);
        $this->addElement($e);
        
        $mUbigeo = new Businessman_Model_Ubigeo();
        $level = 1;
        //var_dump($this->_ubigeoTree);
        foreach($this->_ubigeoTree as $ubigeoLevelName) {
            $e = new Zend_Form_Element_Select('ubigeo_'.$level);    
            //$e->setAttrib('data-child', 'ubigeo_'.($level + 1));
            $e->setRequired(true);
            if($level == 1) 
                $e->setMultiOptions($mUbigeo->findAllByCountryPairs($this->_idCountry, "", "-".$ubigeoLevelName."-"));
            else 
                $e->setMultiOptions(array("" => "-".$ubigeoLevelName."-"));
            $this->addElement($e); 
            $level++;
        }
        
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