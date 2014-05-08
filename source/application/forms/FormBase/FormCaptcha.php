<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FormCaptcha
 *
 * @author marrselo
 */
class Application_Form_FormBase_FormCaptcha extends Zend_Form
{
    
    static function  elementCaptcha($form=null){
        
            
        $captchaFont = CAPTCHA_FONT; // $dataCaptcha['font'];
//        echo $captchaFont;exit;
        $captchaImg = CAPTCHA_IMG;
        $captchaImgUrl = CAPTCHA_URL;
        
        $captcha= new Zend_Form_Element_Captcha('captcha', array(
                'id'            =>'captchas',
                'title'         =>'',
                'class'         => 'captcha inputbox ',
                'captcha'       => array(
                    'width'         => 215,
                    'height'        =>  46,
                    'captcha'       => 'Image',
                    'required'      => true,
                    'font'          => $captchaFont,
                    'wordlen'       => '4',
                    'ImgAlign'      => 'left',
                    'imgdir'        => $captchaImg,
                    'DotNoiseLevel' => '4',
                    'LineNoiseLevel'=> '4', 
                    'fontsize'      => '30',
                    'gcFreq'        => '10',
                    'ImgAlt'        => 'Código de Verificación',
                    'imgurl'        => $captchaImgUrl
                )));
        if(empty($form)) $form = new Zend_Form ();
        $captcha_value = $form->createElement('hidden', 'captcha_value');
        
        
        
        $form->addElement($captcha);
        $form->addElement($captcha_value);  
        $form->getElement('captcha')->removeDecorator('label');
        $form->getElement('captcha')->removeDecorator('htmlTag');
        $form->getElement('captcha_value')->removeDecorator('htmlTag');
        $form->getElement('captcha_value')->removeDecorator('label');
        $form->getElement('captcha')->setAttrib('placeholder','Ingrese el código de la Imagen');
        
        return $form;
    }
}
