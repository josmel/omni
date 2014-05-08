<?php

class Challenge_Form_StepOneA extends Core_Form_Form
{
    public function init() {
        $seoFilter=new Core_Utils_SeoUrl();
        
        $primaryKey = 0;
        
        $this->setMethod('post');      
        $this->setEnctype('multipart/form-data');
//        $this->setAttrib('idbanner',$primaryKey);
        $this->setAction('/inscription/step-one-a');

        $e = new Zend_Form_Element_Select('deporte');    
        $e->setMultiOptions(array(1 => 'Si', 2 => 'No'));
        $this->addElement($e);     

        $e = new Zend_Form_Element_Text('talla', array(
            'required'   => true,
            'label'      => 'talla',
            'filters'    => array('StringTrim'),
            'class' => 'slc-small numeric',
            /*'placeholder' => 'Talla (cm) Ej. 150 ',*/
        ));        
        $this->addElement($e);
        
        $e = new Zend_Form_Element_Text('peso', array(
            'required'   => true,
            'label'      => 'peso',
            'filters'    => array('StringTrim'),
            'class' => 'slc-small decimal',
            /*'placeholder' => 'Peso (Kg)',*/
        )); 
        
//        $this->addElement($e);        
//        $e = new Zend_Form_Element_Text('muneca', array(
//            'required'   => true,
//            'label'      => 'muñeca',
//            'filters'    => array('StringTrim'),
//            'class' => 'slc-small decimal',
//            'placeholder' => 'Muñeca',
//        ));        
        $this->addElement($e);
        
        $e = new Zend_Form_Element_Text('indgrasa', array(
            'required'   => true,
            'label'      => 'muñeca',
            'filters'    => array('StringTrim'),
            'class' => 'slc-medium numeric',
            "readonly"=>true,
            /*'placeholder' => 'Porcentaje grasa Ej. (25)',*/
        ));        
        $this->addElement($e);
        
        
        $rad = new Zend_Form_Element_Radio('sexempr');
        $col = array(
            'M'=>'H',
            'F'=>'M'       
        );
        $rad->addMultiOptions($col);
        $rad->setSeparator('');           
        $rad->setOptions(array(
            'label_class' => 'ioption'
        ));
        $this->addElement($rad);
        
        
        $radio = new Zend_Form_Element_Radio('idtipmusc');
        $options = array(
            'gross'=>'Gruesa',
            'normal'=>'Mediana',
            'thin'=>'Delgada'
        );
        $radio->addMultiOptions($options);
        $radio->setSeparator('');
        $radio->setValue('normal');
        $radio->setOptions(array(
            'label_class' => 'ioption'
        ));
        $this->addElement($radio);
        
        $e = new Zend_Form_Element_Text('cintura', array(
            'required'   => false,
            'label'      => 'cintura',
            'filters'    => array('StringTrim'),
            'class' => 'slc-small numeric',
            'placeholder' => '(Ej. 85)'
        ));        
        $this->addElement($e);
        
        $e = new Zend_Form_Element_Text('cadera', array(
            'required'   => false,
            'label'      => 'cadera',
            'filters'    => array('StringTrim'),
            'class' => 'slc-small numeric',
            'placeholder' => '(Ej. 100)'
        ));        
        $this->addElement($e);
        
        $e = new Zend_Form_Element_Text('cuello', array(
            'required'   => false,
            'label'      => 'cuello',
            'filters'    => array('StringTrim'),
            'class' => 'slc-small numeric',
            'placeholder' => '(Ej. 40)'
        ));        
        $this->addElement($e);
              

        foreach($this->getElements() as $element) {
            $element->removeDecorator('Label');
            $element->removeDecorator('DtDdWrapper');          
            $element->removeDecorator('HtmlTag');
            $element->removeDecorator('Errors');
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

