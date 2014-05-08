<?php

class Office_HelpController extends Core_Controller_ActionOffice {
    
    public function init() {
        /* Initialize action controller here */
        parent::init();
    }

    public function indexAction() {
        
        $mBlog = new Admin_Model_Help();
        $this->view->Help = $mBlog->findAll();
       
    }
}

