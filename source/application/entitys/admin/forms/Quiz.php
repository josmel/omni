<?php

class Admin_Form_Quiz extends Core_Form_Form {

    public function init() {

        $obj = new Application_Model_DbTable_Quiz();
        $primaryKey = $obj->getPrimaryKey();

        $this->setMethod('post');
        $this->setEnctype('multipart/form-data');
        $this->setAttrib('idencuesta', $primaryKey);
        $this->setAction('/quiz/new');

        $e = new Zend_Form_Element_Hidden($primaryKey);
        $this->addElement($e);

        $e = new Zend_Form_Element_Select('alternativa');    
        $this->addElement($e); 
        
        $e = new Zend_Form_Element_Text('pregunta');
        $this->addElement($e);

        $e = new Zend_Form_Element_Textarea('descripcion');
        $this->addElement($e);

        $e = new Zend_Form_Element_Checkbox('vchestado');
        $e->setValue(false);
        $this->addElement($e);


        foreach ($this->getElements() as $element) {
            $element->removeDecorator('Label');
            $element->removeDecorator('DtDdWrapper');
            $element->removeDecorator('HtmlTag');
        }
    }
    public function populate(array $values) {
        if (isset($values['vchestado']))
            $values['vchestado'] = $values['vchestado'] == 'A' ? 1 : 0;

        parent::populate($values);
    }

}