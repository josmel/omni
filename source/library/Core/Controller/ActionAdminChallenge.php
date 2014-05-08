<?php

class Core_Controller_ActionAdminChallenge extends Core_Controller_Action {
                    
    protected $_identity;
    
    public function init() {
        parent::init();
        $this->_helper->layout->setLayout('layout-admin-challenge'); 
        
        if(Zend_Auth::getInstance()->hasIdentity()) {
            $this->_identity = Zend_Auth::getInstance()->getIdentity();
            $this->view->identity = $this->_identity;
        } else {
            if($this->getRequest()->getControllerName() != 'index') 
                $this->_redirect('/');
        }
        
    }

    public function preDispatch()
    {                
        parent::preDispatch();
        $this->view->controller = $this->getRequest()->getControllerName(); 
        $this->view->action = $this->getRequest()->getActionName();
    }
    
    function getMenu() {
        $menu = array(
            'send-message'=>
            array('class'=>'icad-dashb','url'=>'/dashboard','title'=>'Enviar Mensaje'),
            'user'=>
            array('class'=>'icad-prom','url'=>'/user','title'=>'Enviar Alerta'),
            'role'=>
            array('class'=>'icad-prom','url'=>'/role','title'=>'Reactivar'),
            'line'=>
            array('class'=>'icad-prom','url'=>'/line','title'=>'Lineas'),
            'blog'=>
            array('class'=>'icad-prom','url'=>'/blog','title'=>'Blog'),
            'help'=>
            array('class'=>'icad-prom','url'=>'/help','title'=>'Ayuda')
        );
        return $menu;
    }
    
    public function auth($usuario,$password)
    {              
        $dbAdapter = Zend_Registry::get('dbChallenge');
        $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
        $authAdapter
            ->setTableName('tuseradmin')
            ->setIdentityColumn('usuario')
            ->setCredentialColumn('contrasena')
            ->setIdentity($usuario)
            ->setCredential($password);
        $select = $authAdapter->getDbSelect();
        $select->where("vchestado LIKE 'A'");
        
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
    }
}
