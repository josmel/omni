<?php

class Admin_Form_File extends Core_Form_Form
{
    public function init() {
        $seoFilter=new Core_Utils_SeoUrl();
        
        $objType=new Admin_Model_FileType();
        $obj=new Application_Model_DbTable_File();
        $primaryKey=$obj->getPrimaryKey();
        
        $this->setMethod('post');      
        $this->setEnctype('multipart/form-data');
        $this->setAttrib('idfile',$primaryKey);
        $this->setAction('/file/new');
        
        $e = new Zend_Form_Element_Hidden($primaryKey);                         
        $this->addElement($e);
    
        $e = new Zend_Form_Element_Select('codtfile');    
        $e->setMultiOptions($objType->getPairsAll());
        $this->addElement($e);     
        
        $e = new Zend_Form_Element_Text('titulo');        
        $this->addElement($e);
        
        $e = new Zend_Form_Element_Textarea('descripcion');        
        $this->addElement($e);
        
        $i = new Zend_Form_Element_File('nombre');
        $i->setMaxFileSize(10485760);
        $this->addElement($i);                                    
        $this->getElement('nombre')
           ->setDestination(ROOT_IMG_DINAMIC.'/file/origin/')                
           ->addValidator('Size', false, 10485760) // limit to 100k
           ->addValidator('Extension', true, 'doc,xls,pdf,zip,jpg,png,jpeg,gif')// only pdf
           ->setRequired(false);

        $e = new Zend_Form_Element_Checkbox('vchestado');        
        $e->setValue(true);
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
        
        if(isset($values['nombre'])) 
            $values['nombre'] = ROOT_IMG_DINAMIC.'/banner/'.$values['nombre'];
        
        parent::populate($values);
    }
}

