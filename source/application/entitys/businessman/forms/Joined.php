<?php

class Businessman_Form_Joined extends Zend_Form{
    /**
     *
     * @var type array
     */
    protected $_dataCaptcha=array();
    
//    
//    public function __construct($dataCaptcha) {
//        $this->_dataCaptcha=$dataCaptcha;
//    }
    protected $_state = '';
    
    public function init() {
                
        $seoFilter=new Core_Utils_SeoUrl();
        
        $obj=new Application_Model_DbTable_Joined();
        $primaryKey=$obj->getPrimaryKey();
        
        $this->setMethod('post');      
        $this->setEnctype('multipart/form-data');
        $this->setAttrib('idcliper',$primaryKey);
        
        $e = new Zend_Form_Element_Hidden($primaryKey);                         
        $this->addElement($e);
    

        $e = new Zend_Form_Element_Text('name');
        $e->setAttrib('class', 'inpt-medium');
        $e->setRequired(true);
        $e->setAttrib('placeholder', 'Nombres');
        $this->addElement($e);     
        
        $e = new Zend_Form_Element_Text('lastname');        
        $e->setRequired(true);
        $e->setAttrib('class', 'inpt-medium');
        $e->setAttrib('placeholder', 'Apellidos');
        $this->addElement($e);
        
        $e = new Zend_Form_Element_Text('email');        
        $v = new Zend_Validate_EmailAddress();
        $e->addValidator($v);
        $e->setRequired(true);
        $e->setAttrib('class', 'inpt-medium');
        $e->setAttrib('placeholder', 'Email');
        $e->setAttrib('autocomplete', 'off');
        $this->addElement($e);     
        
        $e = new Zend_Form_Element_Password('password');        
        $e->setAttrib('class', 'inpt-medium');
        $e->setAttrib('placeholder', 'Contraseña');
        $e->setAttrib('autocomplete', 'off');
        $this->addElement($e);   
        
        $e = new Zend_Form_Element_Password('confirm');
        $v = new Core_Form_Validate_ConfirmPassword($this->getElement('password'));
        $e->addValidator($v);
        $e->setAttrib('class', 'inpt-medium');
        $e->setAttrib('placeholder', 'Confirmar Contraseña');
        $this->addElement($e);   
        
        $e = new Zend_Form_Element_Text('ndoc');        
        $v = new Zend_Validate_Int();
        $e->addValidator($v);
        $e->setRequired(true);
        $e->setAttrib('class', 'inpt-medium');
        $e->setAttrib('placeholder', 'Documento de Identidad');
        $this->addElement($e); 
        
        $e = new Zend_Form_Element_Text('birthdate'); 
        $e->setRequired(true);
        $e->setAttrib('class', 'inpt-medium datepicker');
        $e->setAttrib('placeholder', 'Fecha de Nacimiento');
        $this->addElement($e); 
        
        
        $e = new Zend_Form_Element_Radio('gender');    
        $e->setRequired(true);
        $e->setMultiOptions(array('M' => 'Masculino', 'F' => 'Femenino'));
        $e->setSeparator('');
        $e->setValue('M');
        $e->setAttrib('label_class', 'ioption');
        $this->addElement($e);     
        
        
        $e = new Zend_Form_Element_Radio('civilstate');    
        $e->setRequired(true);
        $e->setMultiOptions(array('S' => 'Soltero', 'C' => 'Casado'));
        $e->setSeparator('');
        $e->setValue('S');
        $e->setAttrib('label_class', 'ioption');
        $this->addElement($e);
        
        
//        $captchaFont = CAPTCHA_FONT; // $dataCaptcha['font'];
//        $captchaImg = CAPTCHA_IMG;
//        $captchaImgUrl = CAPTCHA_URL;
//        
//        $captcha= new Zend_Form_Element_Captcha('captcha', array(
//                'id'            =>'captchas',
//                'title'         =>'',
//                'class'         => 'captcha inputbox ',
//                'captcha'       => array(
//                    'captcha'       => 'Image',
//                    'required'      => true,
//                    'font'          => $captchaFont,
//                    'wordlen'       => '4',
//                    'ImgAlign'      => 'left',
//                    'imgdir'        => $captchaImg,
//                    'DotNoiseLevel' => '5',
//                    'LineNoiseLevel'=> '5', 
//                    'fontsize'      => '30',
//                    'gcFreq'        => '10',
//                    'ImgAlt'        => 'Código de Verificación',
//                    'imgurl'        => $captchaImgUrl
//                )));
//        
//        $captcha_value = $form->createElement('hidden', 'captcha_value');
//        
//        $form->addElement($captcha);
//        $form->addElement($captcha_value);  
//        $form->getElement('captcha')->removeDecorator('label');
//        $form->getElement('captcha_value')->removeDecorator('htmlTag');
//        $form->getElement('captcha_value')->removeDecorator('label');                
//        return $form;
        
        
        
        
        foreach($this->getElements() as $element) {
            $element->removeDecorator('Label');
            $element->removeDecorator('DtDdWrapper');          
            $element->removeDecorator('HtmlTag');
        }
//        echo $this->_dataCaptcha['font'];
    }
    
    public function isValid($data) {
        $isValid = parent::isValid($data);
        $this->getElement('captcha')->removeDecorator('errors');
        //var_dump($this->getElement('birthdate')->getErrors());
        return $isValid;
    }
        
    public function setState($state) {
        $this->_state = $state;
        if($state == 'new') {
            $this->getElement('password')->setRequired(true);
            $this->getElement('confirm')->setRequired(true);
        } else if($state == 'edit') {
            $this->getElement('password')->setRequired(false);
            $this->getElement('confirm')->setRequired(false);
        }
    }
}