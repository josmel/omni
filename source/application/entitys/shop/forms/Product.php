<?php

class Shop_Form_Product extends Zend_Form{
    /**
     *
     * @var type array
     */
    protected $_dataCaptcha=array();
    
//    
//    public function __construct($dataCaptcha) {
//        $this->_dataCaptcha=$dataCaptcha;
//    }
    protected $_state = '';
    
    public function init() {
                
        $seoFilter=new Core_Utils_SeoUrl();
        
        $obj=new Application_Model_DbTable_Product();
        $primaryKey=$obj->getPrimaryKey();
        
        $this->setMethod('post');      
        $this->setEnctype('multipart/form-data');
        $this->setAttrib('codprod',$primaryKey);
        
        $e = new Zend_Form_Element_Hidden($primaryKey);                         
        $this->addElement($e);
    
        
        $e = new Zend_Form_Element_Text('type');
        $e->setAttrib('class', 'inpt-medium');
        $e->setAttrib('readonly', 'readonly');
        $this->addElement($e);   

        $e = new Zend_Form_Element_Text('desprod');
        $e->setAttrib('class', 'inpt-large');
        $e->setRequired(true);
        $this->addElement($e);     
        
        $e = new Zend_Form_Element_Text('abrprod');
        $e->setAttrib('class', 'inpt-large');
        $e->setRequired(true);
        $this->addElement($e);   
        
        $e = new Zend_Form_Element_Text('shorttext');        
        $e->setRequired(true);
        $e->setAttrib('class', 'inpt-large');
        $this->addElement($e);
        
        $e = new Zend_Form_Element_Textarea('text');        
        $e->setRequired(true);
        $e->setAttrib('class', 'inpt-large');
        $this->addElement($e);
        
        $i = new Zend_Form_Element_File('catalog_image');
        
        $this->addElement($i);                                    
        $this->getElement('catalog_image')
           ->setDestination(ROOT_IMG_DINAMIC.'/product/origin-catalog/')                
           ->addValidator('Size', false, 10024000) // limit to 100k
           ->addValidator('Extension', true, 'jpg,png,gif,jpeg')// only JPEG, PNG, and GIFs
           ->setRequired(false);
        
        $i = new Zend_Form_Element_File('detail_image');
        
        $this->addElement($i);                                    
        $this->getElement('detail_image')
           ->setDestination(ROOT_IMG_DINAMIC.'/product/origin-detail/')                
           ->addValidator('Size', false, 10024000) // limit to 100k
           ->addValidator('Extension', true, 'jpg,png,gif,jpeg')// only JPEG, PNG, and GIFs
           ->setRequired(false);
        
        
        $e = new Zend_Form_Element_Checkbox('issalient');        
        $e->setValue(true);
        $this->addElement($e);     
        
        foreach($this->getElements() as $element) {
            $element->removeDecorator('Label');
            $element->removeDecorator('DtDdWrapper');          
            $element->removeDecorator('HtmlTag');
        }
//        echo $this->_dataCaptcha['font'];
    }
    
    public function isValid($data) {
        $isValid = parent::isValid($data);
        //var_dump($this->getElement('birthdate')->getErrors());
        return $isValid;
    }
}