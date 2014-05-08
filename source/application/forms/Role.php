<?php

class Application_Form_Role extends Zend_Form
{
    public function init() {
        $seoFilter=new Core_Utils_SeoUrl();
        
        $objAcl=new Application_Model_Acl();
        $obj=new Application_Model_DbTable_Role();
        $primaryKey=$obj->getPrimaryKey();
        
        $this->setMethod('post');      
        $this->setEnctype('multipart/form-data');
        $this->setAttrib('idrol',$primaryKey);
        $this->setAction('/role/new');
        
        $e = new Zend_Form_Element_Hidden($primaryKey);                         
        $this->addElement($e);
    

        $e = new Zend_Form_Element_Text('desrol');        
        $this->addElement($e);     
        
        $e = new Zend_Form_Element_Checkbox('state');        
        $e->setValue(true);
        $this->addElement($e);     
        
        $e = new Zend_Form_Element_MultiCheckbox('acls');    
        $e->setMultiOptions($objAcl->getFetchPairsAllAcls());
        $this->addElement($e);     
        
        
        foreach($this->getElements() as $element) {
            $element->removeDecorator('Label');
            $element->removeDecorator('DtDdWrapper');          
            $element->removeDecorator('HtmlTag');
        }
    }
    
    
    public function populate(array $values) {
        parent::populate($values);
    
        if(isset($values['idrol'])) {
            $objAcl=new Application_Model_Acl();
            $selectedAcls = $objAcl->getFetchPairsAclByRole($values['idrol']);
            $this->getElement('acls')->setValue(array_keys($selectedAcls));
        }
    }
}