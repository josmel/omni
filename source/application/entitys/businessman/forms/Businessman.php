<?php

class Businessman_Form_Businessman extends Zend_Form {

    public function init() {


        $obj = new Application_Model_DbTable_Businessman();
        $primaryKey = $obj->getPrimaryKey();

        $this->setMethod('post');
        $this->setEnctype('multipart/form-data');
        $this->setAttrib('codempr', $primaryKey);
        $this->setAction('/web/edit');

        $e = new Zend_Form_Element_Hidden($primaryKey);
        $this->addElement($e);
       // $this->addAttribs($attribs);

        $e = new Zend_Form_Element_Text('urlfacebook');
        $e->setAttrib("class", "fixFb");
        $this->addElement($e);

        $e = new Zend_Form_Element_Text('urltwitter');
        $e->setAttrib("class", "fixTwt");
        $this->addElement($e);

        $e = new Zend_Form_Element_Text('urlyoutube');
        $e->setAttrib("class", "fixYtb");
        $this->addElement($e);
        
        $e = new Zend_Form_Element_File('picture');
        $this->addElement($e);
     

        $e = new Zend_Form_Element_Text('urlblog');
        $this->addElement($e);

        $e = new Zend_Form_Element_Textarea('testimony');
        $this->addElement($e);

        $e = new Zend_Form_Element_Text('subdomain');
        $this->addElement($e);

        foreach ($this->getElements() as $element) {
            $element->removeDecorator('Label');
            $element->removeDecorator('DtDdWrapper');
            $element->removeDecorator('HtmlTag');
        }
//        echo $this->_dataCaptcha['font'];
    }

}