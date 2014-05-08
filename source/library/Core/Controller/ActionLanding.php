<?php

class Core_Controller_ActionLanding extends Core_Controller_Action {
    
    protected $_businessman = null;
    
    protected $_joined = null;
    
    public function init() {
        parent::init();
        $this->_helper->layout->setLayout('layout-landing');  
        
        $this->_joined = Zend_Auth::getInstance()->getIdentity();
        $this->view->joined = $this->_joined;
        
        $carrito = Store_Cart_Factory::createInstance();
        $this->view->totalCart = $carrito->getProducts()->count();
        
        $mBg = new Admin_Model_Background();
        $this->view->bg1 = $mBg->findAllByType('FONDO1', true);
        $this->view->bg2 = $mBg->findAllByType('FONDO1', true);
        $this->view->bgM = $mBg->findAllByType('FONDOM', true);
        
        $backgroundColorCode = $this->_config['app']['backgroundColor']['fondoLanding'];
        $mBackgroundType = new Admin_Model_BackgroundType();
        $dataColor = $mBackgroundType->findBCById($backgroundColorCode);
        $this->view->backgroundColor = $dataColor['nombre']; 
        
        if (!$this->isIndependentAction()) {
            $findSubdomain = false;
            $subDomain = Zend_Registry::get('subdomain');
            if($subDomain != '') {
                $cache = Zend_Registry::get('Cache');
                $cacheName = $subDomain.'_businessman';                
                if (!$this->_businessman = $cache->load($cacheName)) {
                    $modelBusinessman = new Businessman_Model_Businessman();
                    $this->_businessman = 
                        $modelBusinessman->getBySubdomainFromView($subDomain);                    
                    if(!empty($this->_businessman)) { 
                        if(empty($this->_businessman['picture'])) {
                            $this->_businessman['picture'] = 
                                $this->_config['app']['defaults']['imageProfile'];
                        }

//                        $this->_businessman['picture'] = 
//                            $this->_businessman['picture'];
                        $this->view->businessman = $this->_businessman;

                        $cache->save($this->_businessman, $cacheName);
                        $findSubdomain = true;
                    }
                } else {
                    $this->view->businessman = $this->_businessman;
                    $findSubdomain = true;
                }

                //$this->_businessman['iva'] = 0.18;
                //var_dump($this->_businessman); exit;
            }  
            if (!$findSubdomain) {
                $this->getRequest()->setControllerName('error');
                $this->getRequest()->setActionName('error');
                throw new Exception("No se encontró vendedor."); 
            } else {
                $this->addYosonVar('iva', $this->_businessman['iva']);
                $this->addYosonVar('longitudDoc', $this->_businessman['tdopais']);
                $this->addYosonVar('monSymbol', $this->_businessman['simbolo']);
            }
        }
    }
    
    public function preDispatch() {
        parent::preDispatch();
        $this->view->menu=$this->getMenu();
    }
    
    public function postDispatch() {        
        parent::postDispatch();
    }
    
    function getMenu() {
        $menu = array(
            'index'=>
            array('class'=>'', 'snav' => true, 'hashtag' => 'home',
                  'url'=>'/','title'=>'INICIO'),
            'product'=>
            array('class'=>'', 'snav' => true, 'hashtag' => 'product', 
                  'url'=>'/','title'=>'PRODUCTOS'),
            'oportunity'=>
            array('class'=>'', 'snav' => true, 'hashtag' => 'oportunity', 
                  'url'=>'/','title'=>'OPORTUNIDAD'),
            'company'=>
            array('class'=>'', 'snav' => true, 'hashtag' => 'company', 
                  'url'=>'/','title'=>'COMPAÑIA'),
            'contact'=>
            array('class'=>'', 'snav' => true, 'hashtag' => 'contact', 
                  'url'=>'/','title'=>'CONTÁCTAME'),
            'cart'=>
            array('class'=>'', 'snav' => false, 'hashtag' => '', 'li_class' => 'trgcart',
                  'url'=>'/cart','title'=>'CARRITO')/*,
            'account'=>
            array('class'=>'', 'snav' => false, 'hashtag' => '', 
                  'url'=>'/account','title'=>'MI CUENTA')*/
        );
        return $menu;
    }
    
    public function auth($usuario,$password,$url=null) {              
        $dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
        $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
        $authAdapter
            ->setTableName('tclientepersonal')
            ->setIdentityColumn('email')
            ->setCredentialColumn('password')
            ->setIdentity($usuario)
            ->setCredential($password);
        try {
            $select = $authAdapter->getDbSelect();
            $select->where("vchestado LIKE 'A' and codempr LIKE ?", $this->_businessman['codempr']);
            $result = Zend_Auth::getInstance()->authenticate($authAdapter);
            if ($result->isValid()){
                $storage = Zend_Auth::getInstance()->getStorage();
                $bddResultRow = $authAdapter->getResultRowObject();
                $storage->write($bddResultRow);
                $msj = 'Bienvenido Usuario '.$result->getIdentity();
                //$this->_flashMessenger->success($msj);
                $this->_joined = Zend_Auth::getInstance()->getIdentity(); 
                $this->view->joined = $this->_joined;
                if(!empty($url)){
                    $this->_redirect($url);
                }
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
                //$this->_flashMessenger->warning($msj);
               
                $return = false;
            }
        } catch(Exception $e) {
            echo $e->getMessage();exit;
        }
        
        return $return;
    }
    
    
    public function getBusinessMan() {
        return $this->_businessman;
    }
    
    public function isIndependentAction() {
        $enabled = false;
        $controllerName = $this->getRequest()->getControllerName();
        $actionName = $this->getRequest()->getActionName();
        
        switch ($controllerName) {
            case "service": 
                switch ($actionName) {
                    case "alignet" : $enabled = true; break;
                }
                break;
        }
        
        return $enabled;
    }
}
