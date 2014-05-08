<?php

class Admin_IndexController extends Core_Controller_ActionAdmin       
{
    public function init() {
        parent::init();
    }
    public function indexAction()
    {                   
        //$this->_layout->setLayout('layout-login');
        
    } 
    public function loginAction()
    {
        $this->_helper->layout()->disableLayout();
        if ($this->_request->isPost()){
            $params = $this->_getAllParams();            
            $login=$this->auth($params['login'],$params['password']);            
            if($login){              
                $this->_redirect('/vo/index/dashboard');
            }else{         
                $this->_redirect('/vo/index');
            }
        }
    }
    public function deleteAction()
    {
        
    }
    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_redirect('/vo');
    }
    
}

