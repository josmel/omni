<?php

class Core_Controller_ActionAdmin extends Core_Controller_Action {
                    
    protected $_identity;
    public function init() {
        parent::init();
        //$this->_helper->layout->setLayout('layout-admin');        
    }
/*
    public function preDispatch()
    {        
        $this->_identity = Zend_Auth::getInstance()->getIdentity();            
        $this->view->controller=$this->getRequest()->getControllerName(); 
        $this->view->action=$this->getRequest()->getActionName();
        $this->view->identity=$this->_identity;
        $this->permisos();
    }
    
    function permisos()
    {
        $auth = Zend_Auth::getInstance();
        $controller=$this->_request->getControllerName();
        if ($auth->hasIdentity()) {                    
        }else{
            if ($controller!='index') {
            $this->_redirect('/admin');
            }
        }
        
    }
    
    public function auth($usuario,$password)
    {              
            $dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
            $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
            $authAdapter
                ->setTableName('user_admin')
                ->setIdentityColumn('user_admin_user')
                ->setCredentialColumn('user_admin_password')
                ->setIdentity($usuario)
                ->setCredential($password);
            $select = $authAdapter->getDbSelect();
            $select->where('user_admin_activo = 1');             
            $result = Zend_Auth::getInstance()->authenticate($authAdapter);
            if ($result->isValid()){          
                $storage = Zend_Auth::getInstance()->getStorage();
                $bddResultRow = $authAdapter->getResultRowObject();
                $storage->write($bddResultRow);
                $msj = 'Bienvenido Usuario '.$result->getIdentity();
                $this->_flashMessenger->success($msj);
                $this->_identity = Zend_Auth::getInstance()->getIdentity();                
                $return = true;
            } else {                
                switch ($result->getCode()) {
                    case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
                        $msj = 'El usuario no existe';
                        break;
                    case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
                        $msj = 'Password incorrecto';
                        break;
                    default:
                        $msj='Datos incorrectos';
                        break;
                }
               $this->_flashMessenger->warning($msj);
               
                $return = false;
            }
             return $return;
    }*/
}
