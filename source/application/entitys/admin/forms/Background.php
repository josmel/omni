<?php

class Admin_Form_Background extends Core_Form_Form
{
    public function init() {
        
        $objBType=new Admin_Model_BackgroundType();
        $obj = new Application_Model_DbTable_Background();
        $primaryKey=$obj->getPrimaryKey();
        
        $this->setMethod('post');      
        $this->setEnctype('multipart/form-data');
        $this->setAttrib('idfondo',$primaryKey);
        $this->setAction('/background/new');
        
        $e = new Zend_Form_Element_Hidden($primaryKey);                         
        $this->addElement($e);
    
        $e = new Zend_Form_Element_Select('codtfondo');    
        $e->setMultiOptions($objBType->getPairsAll());
        $this->addElement($e);     
        
        $i = new Zend_Form_Element_File('imagen');
        
        $this->addElement($i);                                    
        $this->getElement('imagen')
           ->setDestination(ROOT_IMG_DINAMIC.'/background/origin/')                
           ->addValidator('Size', false, 10024000) // limit to 100k
           ->addValidator('Extension', true, 'jpg,png,gif,jpeg')// only JPEG, PNG, and GIFs
           ->setRequired(false);

        $e = new Zend_Form_Element_Text('titulo');        
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
        
        if(isset($values['imagen'])) 
            $values['imagen'] = ROOT_IMG_DINAMIC.'/banner/'.$values['imagen'];
        
        parent::populate($values);
    }
}

