<?php

class Admin_Form_Video extends Core_Form_Form
{
    public function init() {
        $objType = new Admin_Model_VideoType();
        $obj = new Application_Model_DbTable_Video();
        $primaryKey = $obj->getPrimaryKey();
        
        $e = new Zend_Form_Element_Select('codtvideo');    
        $e->setMultiOptions($objType->getPairsAll());
        $this->addElement($e); 
        
        $this->setMethod('post');      
        $this->setEnctype('multipart/form-data');
        $this->setAttrib('idvideo',$primaryKey);
        $this->setAction('/video/new');
        
        $e = new Zend_Form_Element_Hidden($primaryKey);                         
        $this->addElement($e);
    
        $e = new Zend_Form_Element_Text('url');        
        $this->addElement($e);     
        
        $e = new Zend_Form_Element_Text('titulo');        
        $this->addElement($e);
        
        $e = new Zend_Form_Element_Textarea('descripcion');        
        $this->addElement($e);     
        
        $e = new Zend_Form_Element_Checkbox('vchestado');        
        $e->setValue(false);
        $this->addElement($e);     
            
        
        foreach($this->getElements() as $element) {
            $element->removeDecorator('Label');
            $element->removeDecorator('DtDdWrapper');          
            $element->removeDecorator('HtmlTag');
        }
    }
    
    
    public function populate(array $values) {
        if(isset($values['vchestado'])) 
            $values['vchestado'] = $values['vchestado'] == 'A' ? 1 : 0;
        
        parent::populate($values);
    }
}