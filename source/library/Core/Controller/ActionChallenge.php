<?php

class Core_Controller_ActionChallenge extends Core_Controller_Action {
                    
    protected $_identity;
    
    public function init() {
        parent::init();
        //$this->_helper->layout->setLayout('layout-admin');        
    }

    public function preDispatch() {      
        parent::preDispatch();
        //var_dump($authDesafio->ciclo);
//        Zend_Session::namespaceUnset('authDesafio'); 
//        Zend_Session::namespaceUnset('step'); exit;
        
//        Zend_Debug::dump($authDesafio->auth);exit;
        if ($this->_getParam('session', '') != '') {
            Zend_Session::namespaceUnset('authDesafio'); 
            Zend_Session::namespaceUnset('step');
        }
        
        $authDesafio = new Zend_Session_Namespace('authDesafio');
        
        if(empty($authDesafio->auth)){
            $codSession = $this->_getParam('session', '');
            $mSession = new Businessman_Model_BusinessmanSession();
            $sessionData = $mSession->findBySessionCode($codSession);

            if(empty($sessionData)) {
                $this->accessDenied();
            }
            //var_dump($sessionData);
            $mBusinessman = new Businessman_Model_Businessman();
            $businessman = $mBusinessman->findDataViewById($sessionData['codempr']);

            $mWeek = new Biller_Model_Week();
            $week = $mWeek->getLastWeek();
            
            $billerHelper = $this->getHelper('billerServices');
            $dataBusinessman = $billerHelper->getDataBusinessman(
                    $businessman['codempr'], 
                    $week['codsema'], 
                    $this->_config['app']['service']['prolife']
            );
            
            if(!empty($dataBusinessman)) $dataBusinessman['activo'] = false;
            
            if(!$dataBusinessman['activo']) {
                $dataBusinessman['activo'] = true;
                //$this->accessDenied();
                //echo "<pre><h1>NO ACCESS - NO ACCESS - NO ACCESS - NO ACCESS - NO ACCESS - NO ACCESS - NO ACCESS - NO ACCESS - NO ACCESS - NO ACCESS</h1></pre>";
            }
                
            $auth = array(
                'codemp' => $businessman['codempr'],
                'name' => $businessman['nomempr'],
                'weekCode' => $week['codsema'],
                'lastName' => $businessman['appempr'].' '.$businessman['apmempr'],
                'appemp' => $businessman['appempr'],
                'apmemp' => $businessman['apmempr'],
                'email' => $businessman['emaempr'],
                'celular' => $businessman['celular'],
                'telefono' => $businessman['telefono'],
                'picture' => $businessman['picture'],
                'sexemp' => $businessman['sexempr'],
                'officeActive' => $dataBusinessman['activo'],
                'fuxionActive' => false,
                'active' => false
            );
            
            $authDesafio = new Zend_Session_Namespace('authDesafio');
            $authDesafio->auth = $auth;
            $authDesafio->empActive = $dataBusinessman['activo'];
            
            $this->_identity = $authDesafio->auth;
            //exit;
        } else {
            $this->_identity = $authDesafio->auth;
            //var_dump($authDesafio->auth); exit;
        }
        $this->view->identity = $this->_identity;
    }
    /*
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
    */
    
    function accessDenied() {
        $iniFile = APPLICATION_PATH . "/configs/office.ini";
        if (is_readable($iniFile)) {
            $officeConfig = new Zend_Config_Ini($iniFile, APPLICATION_ENV);
            $officeConfig = $officeConfig->toArray();
        } else {
            throw new Zend_Exception('Error de configuración.');
        }
        $url = $officeConfig['app']['siteUrl'];
        $this->redirect($url);
    }
}
