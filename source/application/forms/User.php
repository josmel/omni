<?php

class Application_Form_User extends Zend_Form
{
    public function init() {
        $seoFilter=new Core_Utils_SeoUrl();
        
        $objRoles=new Application_Model_Role();
        $obj=new Application_Model_DbTable_User();
        $primaryKey=$obj->getPrimaryKey();
        
        $this->setMethod('post');      
        $this->setEnctype('multipart/form-data');
        $this->setAttrib('iduser',$primaryKey);
        $this->setAction('/user/new');
        
        $e = new Zend_Form_Element_Hidden($primaryKey);                         
        $this->addElement($e);
    

        $e = new Zend_Form_Element_Text('name');        
        $this->addElement($e);     
        
        $e = new Zend_Form_Element_Text('apepat');        
        $this->addElement($e);     
        
        $e = new Zend_Form_Element_Text('apemat');        
        $this->addElement($e);     
        
        $e = new Zend_Form_Element_Text('login');        
        $this->addElement($e);     
        
        $e = new Zend_Form_Element_Password('password');        
        $this->addElement($e);   
        
        $e = new Zend_Form_Element_Text('email');        
        $this->addElement($e);     
        
        $e = new Zend_Form_Element_Checkbox('state');        
        $e->setValue(true);
        $this->addElement($e);     
        
        $e = new Zend_Form_Element_MultiCheckbox('roles');    
        $e->setMultiOptions($objRoles->getFetchPairsAllRoles());
        $this->addElement($e);     
        
        
        foreach($this->getElements() as $element) {
            $element->removeDecorator('Label');
            $element->removeDecorator('DtDdWrapper');          
            $element->removeDecorator('HtmlTag');
        }
    }
    
    
    public function populate(array $values) {
        parent::populate($values);
    
        if(isset($values['iduser'])) {
            $objRoles=new Application_Model_Role();
            $selectedRoles = $objRoles->getRolesByUser($values['iduser']);
            $this->getElement('roles')->setValue(array_keys($selectedRoles));
        }
    }
}