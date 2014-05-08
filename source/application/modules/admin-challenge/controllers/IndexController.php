<?php

class AdminChallenge_IndexController extends Core_Controller_ActionAdminChallenge {
    
    public function init() {
        parent::init();      
    }
    
    public function indexAction() {
        if(Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect('/challenge');
        }
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
                $this->_redirect('/challenge');
            }else{
                $this->_redirect('/');
            }
        }else{
            $this->_redirect('/');
        }
    }
    
    public function logoutAction() {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_redirect('/');
    }
}

