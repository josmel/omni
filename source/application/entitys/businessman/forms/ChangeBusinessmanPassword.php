<?php

class Businessman_Form_ChangeBusinessmanPassword extends Zend_Form {
    
    public function init() {
        $seoFilter=new Core_Utils_SeoUrl();
        
        $this->setMethod('post');      
        $this->setEnctype('multipart/form-data');
        
        
        $e = new Zend_Form_Element_Password('claempr');  
        $e->setRequired(true);
        $e->setAttrib('class', 'inpt-medium');
        $e->setAttrib('placeholder', 'Contraseña');
        $this->addElement($e);   
        
        $e = new Zend_Form_Element_Password('confirm');
        $v = new Core_Form_Validate_ConfirmPassword($this->getElement('claempr'));
        $e->addValidator($v);
        $e->setRequired(true);
        $e->setAttrib('class', 'inpt-medium');
        $e->setAttrib('placeholder', 'Confirmar Contraseña');
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