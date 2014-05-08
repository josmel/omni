<?php

class Businessman_Form_UpdateBusinessmanPassword extends Zend_Form {
    
    public function init() {
        $obj = new Application_Model_DbTable_Businessman();
        $primaryKey = $obj->getPrimaryKey();
        $this->setMethod('post');
        $this->setEnctype('multipart/form-data');
        $this->setAttrib('codempr', $primaryKey);
        $this->setAction('/web/update-pass');
        
        $e = new Zend_Form_Element_Password('claempr');  
        $e->setRequired(true);
        $e->setAttrib('class', 'inpt-medium');
        $this->addElement($e);   
        
        $e = new Zend_Form_Element_Password('confirmone');  
        $e->setRequired(true);
        $e->setAttrib('class', 'inpt-medium');
        $this->addElement($e); 
        
        $e = new Zend_Form_Element_Password('confirmtwo');  
        $e->setRequired(true);
        $e->setAttrib('class', 'inpt-medium');
        $this->addElement($e); 
        $e = new Zend_Form_Element_Submit('Cambiar');
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