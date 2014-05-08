<?php

class Office_IndexController extends Core_Controller_ActionOffice       
{
    public function init() {
        parent::init();
    }
    
    public function indexAction()
    {
        //$this->_layout->setLayout('layout-login');
    } 
    
    // Para obtener la IP del visitante
    function getIP(){
        if( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] )) $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if( isset( $_SERVER ['HTTP_VIA'] ))  $ip = $_SERVER['HTTP_VIA'];
        else if( isset( $_SERVER ['REMOTE_ADDR'] ))  $ip = $_SERVER['REMOTE_ADDR'];
        else $ip = null ;
        return $ip;
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

