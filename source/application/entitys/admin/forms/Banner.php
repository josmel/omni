<?php

class Admin_Form_Banner extends Core_Form_Form
{
    public function init() {
        $seoFilter=new Core_Utils_SeoUrl();
        
        $objProj=new Admin_Model_Project();
        $obj=new Application_Model_DbTable_Banner();
        $primaryKey=$obj->getPrimaryKey();
        
        $this->setMethod('post');      
        $this->setEnctype('multipart/form-data');
        $this->setAttrib('idbanner',$primaryKey);
        $this->setAction('/banner/new');
        
        $e = new Zend_Form_Element_Hidden($primaryKey);                         
        $this->addElement($e);
    
        $e = new Zend_Form_Element_Select('codproy');    
        $e->setMultiOptions($objProj->getFetchPairsAllProjects());
        $this->addElement($e);     
        
        $i = new Zend_Form_Element_File('nombre');
        
        $this->addElement($i);                                    
        $this->getElement('nombre')
           ->setDestination(ROOT_IMG_DINAMIC.'/banner/origin/')                
           ->addValidator('Size', false, 10024000) // limit to 100k
           ->addValidator('Extension', true, 'jpg,png,gif,jpeg')// only JPEG, PNG, and GIFs
           ->setRequired(false);

        $e = new Zend_Form_Element_Text('titulo');        
        $this->addElement($e);
        
        $e = new Zend_Form_Element_Textarea('descripcion');        
        $this->addElement($e);
        
        $e = new Zend_Form_Element_Text('fechainicio');        
        $this->addElement($e);    
        
        $e = new Zend_Form_Element_Text('fechafin');        
        $this->addElement($e);    
        
        $e = new Zend_Form_Element_Checkbox('vchestado');        
        $e->setValue(true);
        $this->addElement($e);     
        
        foreach($this->getElements() as $element) {
            $element->removeDecorator('Label');
            $element->removeDecorator('DtDdWrapper');          
            $element->removeDecorator('HtmlTag');
        }
    }
    
    public function populate($values) {
        if(isset($values['vchestado'])) 
            $values['vchestado'] = $values['vchestado'] == 'A' ? 1 : 0;
        
        if(isset($values['nombre'])) 
            $values['nombre'] = ROOT_IMG_DINAMIC.'/banner/'.$values['nombre'];
        
        parent::populate($values);
    }
}

