<?php

class Businessman_Form_BusinessmanPassword extends Zend_Form {
    
    public function init() {
    
        $this->setMethod('post');      
        $this->setEnctype('multipart/form-data');
        $e = new Zend_Form_Element_Text('emaempr');  
        $v = new Zend_Validate_EmailAddress();
        $e->addValidator($v);
        $e->setRequired(true);
        $e->setAttrib('class', 'inpt-medium');
        $e->setAttrib('placeholder', 'Email');
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