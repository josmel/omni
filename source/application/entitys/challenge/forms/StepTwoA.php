<?php

class Challenge_Form_StepTwoA extends Core_Form_Form
{
    public function init() {
        $seoFilter=new Core_Utils_SeoUrl();
        
        $primaryKey = 0;
        
        $this->setMethod('post');      
        $this->setEnctype('multipart/form-data');
//        $this->setAttrib('idbanner',$primaryKey);
        $this->setAction('/inscription/step-two-a');
        

        $e = new Zend_Form_Element_Text('nomemp', array(
            'required'   => true,
            'label'      => 'nomemp',
            'filters'    => array('StringTrim'),
            'class' => 'input-xmedium',
        ));        
        $this->addElement($e);
        
        $e = new Zend_Form_Element_Text('nickemp', array(
            'required'   => true,
            'label'      => 'nickemp',
            'filters'    => array('StringTrim'),
            'class' => 'input-xmedium',
        ));              
        $this->addElement($e);
        
        $e = new Zend_Form_Element_Text('apepaterno', array(
            'required'   => true,
            'label'      => 'apepaterno',
            'filters'    => array('StringTrim'),
            'class' => 'input-xmedium',
        ));              
        $this->addElement($e);
        
        
        $e = new Zend_Form_Element_Text('apematerno', array(
            'required'   => true,
            'label'      => 'apematerno',
            'filters'    => array('StringTrim'),
            'class' => 'input-xmedium',
        ));        
        $this->addElement($e);
        
        $e = new Zend_Form_Element_Text('codefcp', array(
//            'required'   => true,
            'label'      => 'codefcp',
            'filters'    => array('StringTrim'),
            'class' => 'input-xmedium',
            'readonly' => 'readonly'
        ));        
        $this->addElement($e);
        
        $e = new Zend_Form_Element_Text('edad', array(
            'required'   => true,
            'label'      => 'edad',
            'filters'    => array('StringTrim'),
            'class' => 'slc-small numeric inptEdad',
        ));        
        $this->addElement($e);
        
        $e = new Zend_Form_Element_Text('telefono', array(
            'required'   => true,
            'label'      => 'telefono',
            'filters'    => array('StringTrim'),
            'class' => 'input-xmedium',
        ));        
        $this->addElement($e);
        
        $e = new Zend_Form_Element_Text('rpm', array(
//            'required'   => true,
            'label'      => 'rpm',
            'filters'    => array('StringTrim'),
            'class' => 'input-xmedium',
        ));        
        $this->addElement($e);
        
        $e = new Zend_Form_Element_Text('email', array(
            'required'   => true,
            'label'      => 'email',
            'filters'    => array('StringTrim'),
            'class' => 'input-xmedium',
        ));        
        $this->addElement($e);
        
        $e = new Zend_Form_Element_Text('codmentor', array(
            'required'   => true,
            'label'      => 'codmentor',
            'filters'    => array('StringTrim'),
            'class' => 'input-xmedium numeric',
        ));        
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

