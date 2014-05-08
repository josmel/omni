<?php

class Admin_IndexController extends Core_Controller_ActionAdmin {
    
    public function init() {
        parent::init();
        $this->_helper->layout->setLayout('admin/layout-login');        
    }
    
    public function indexAction() {
        
    }
    
    public function loginAction() {
        
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $user=$this->_getParam('txtUsername',"");
        $pass=$this->_getParam('txtPassword',"");
        $pass=md5($pass);
        if ($this->_request->isPost() && $user!="" && $pass!=""){            
            $login=$this->auth($user,$pass);
            if($login){  
                //var_dump(json_encode($this->_identity));exit;
                $this->_redirect('/dashboard');
            }else{
                $this->_redirect('/index');
            }
        }else{
            $this->_redirect('/index');
        }
    }
    
    public function logoutAction() {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_redirect('/');
    }
}

