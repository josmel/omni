<?php

class Challenge_Form_StepOneC extends Core_Form_Form
{
    public function init() {
        $seoFilter=new Core_Utils_SeoUrl();
        
        $primaryKey = 0;
        
        $this->setMethod('post');      
        $this->setEnctype('multipart/form-data');
//        $this->setAttrib('idbanner',$primaryKey);
        $this->setAction('/inscription/step-one-c');
        

//        $e = new Zend_Form_Element_Text('tiembaja', array(
//            'required'   => true,
//            'label'      => 'tiembaja',
//            'filters'    => array('StringTrim'),
//            'class' => 'input-medium',
//        ));        
//        $this->addElement($e);
        
//        $e = new Zend_Form_Element_Text('kilobaja', array(
//            'required'   => true,
//            'label'      => 'kilobaja',
//            'filters'    => array('StringTrim'),
//            'class' => 'input-medium decimal',
//        ));
        

//        $this->addElement($e);
        
        $e = new Zend_Form_Element_Text('indgrasa', array(
            'required'   => true,
            'label'      => 'kilobaja',
            'filters'    => array('StringTrim'),
            'class' => 'input-medium numeric',
        ));              
        $this->addElement($e);
        
        
        $e = new Zend_Form_Element_Text('cintura', array(
            'required'   => true,
            'label'      => 'cintura',
            'filters'    => array('StringTrim'),
            'class' => 'slc-small numeric',
        ));        
        $this->addElement($e);
        
        $e = new Zend_Form_Element_Text('cadera', array(
            'required'   => true,
            'label'      => 'cadera',
            'filters'    => array('StringTrim'),
            'class' => 'slc-small numeric',
        ));        
        $this->addElement($e);
        
        $e = new Zend_Form_Element_Text('espalda', array(
            'required'   => true,
            'label'      => 'espalda',
            'filters'    => array('StringTrim'),
            'class' => 'slc-small numeric',
        ));        
        $this->addElement($e);
        
        $e = new Zend_Form_Element_Text('motivo', array(
            'required'   => true,
            'label'      => 'motivo',
            'filters'    => array('StringTrim'),
            'class' => 'input-large',
        ));        
        $this->addElement($e);
        
        $e = new Zend_Form_Element_Textarea('compromiso', array(
            'required'   => true,
            'label'      => 'compromiso',
            'filters'    => array('StringTrim'),
            'class' => 'txt-large',
        ));        
        $this->addElement($e);
        
        $this->addElement($e);

        foreach($this->getElements() as $element) {
            $element->removeDecorator('Label');
            $element->removeDecorator('DtDdWrapper');          
            $element->removeDecorator('HtmlTag');
        }
    }
    
//    public function populate($values) {
//        if(isset($values['vchestado'])) 
//            $values['vchestado'] = $values['vchestado'] == 'A' ? 1 : 0;
//        
//        if(isset($values['nombre'])) 
//            $values['nombre'] = ROOT_IMG_DINAMIC.'/banner/'.$values['nombre'];
//        
//        parent::populate($values);
//    }
}

