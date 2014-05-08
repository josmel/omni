<?php

class Core_Controller_ActionOffice extends Core_Controller_Action {
    
    protected $_businessman = null;
    
    public function init() {
        parent::init();
        $this->_helper->layout->setLayout('layout-office');

        if(Zend_Auth::getInstance()->hasIdentity()) {
            $this->_businessman = Zend_Auth::getInstance()->getIdentity();
            //$this->_businessman = get_object_vars($this->_businessman); 
        } else {
         
            if($this->getRequest()->getControllerName() != 'index') 
                $this->_redirect('/');
        }
        //var_dump(Zend_Auth::getInstance()->getStorage());
        //var_dump($this->_businessman);
        $this->view->businessman = $this->_businessman;
        $dataCart = Store_Cart_Factory::createInstance();
        $this->view->itemList = $dataCart->getProducts()->getIterator(); 
        $this->view->cartIva = $dataCart->getStep() > 3 ? $dataCart->getIva() :
                                $this->_businessman['iva'];
        
        $this->addYosonVar('totalCartItems', $dataCart->getProducts()->count(), false);
    }
    
    public function preDispatch() {
        parent::preDispatch();
        $this->view->menu = $this->getMenu();
    }
    
    public function postDispatch() {
        parent::postDispatch();
    }
    
    function getMenu() {
        $menu = array(
            
          'web'=>
            array('class'=>'', 'url'=>'/web','title'=>'MI WEB'),
            'help'=>
            array('class'=>'', 'url'=>'/help','title'=>'AYUDA'),
            'product'=>
            array('class'=>'', 'url'=>'/product','title'=>'TIENDA', 'additionalViews' => array('cart', 'quick-cart')),
            'business'=>
            array('class'=>'', 'url'=>'/business','title'=>'MI NEGOCIO'),
            'fuxion'=>
            array('class'=>'', 'url'=>'/fuxion','title'=>'FUXION'),
            'home'=>
            array('class'=>'', 'url'=>'/home','title'=>'INICIO')
        );
        return $menu;
    }
    
    public function auth($usuario,$password,$url=null) {              
        $dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
        $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
        $authAdapter
            ->setTableName('vbusinessman')
            ->setIdentityColumn('codempr')
            ->setCredentialColumn('claempr')
            ->setIdentity($usuario)
            ->setCredential($password);
      
            //var_dump($authAdapter); exit;
        try {
            $result = Zend_Auth::getInstance()->authenticate($authAdapter);
            if ($result->isValid()){
               
                $storage = Zend_Auth::getInstance()->getStorage();
                $bddResultRow = $authAdapter->getResultRowObject();
                $storage->write($bddResultRow);
                $msj = 'Bienvenido Usuario '.$result->getIdentity();
                
                //$this->_flashMessenger->success($msj);
                $this->_businessman = Zend_Auth::getInstance()->getIdentity();
                $this->_businessman = get_object_vars($this->_businessman);                
//                Zend_Auth::getInstance()->getStorage()->write($this->_businessman);
                $mSession = new Businessman_Model_BusinessmanSession();
                $mBuyDocument = new Biller_Model_BuyDocument();
                $codsess = $mSession->sessionStart($this->_businessman['codempr'], $this->_businessman['claempr']);
                $this->view->idsess = $codsess;
                $this->_businessman['codsession'] = $codsess;
                $this->_businessman['historyPoints'] = $mBuyDocument->getPointsByBusinessman($this->_businessman['codempr']);              
                Zend_Auth::getInstance()->getStorage()->write($this->_businessman);               
                $this->view->businessman = $this->_businessman;

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
            
               
                $this->_flashMessenger->warning($msj);
                //echo $msj; //xit;
                $return = false;
            }
        } catch(Exception $e) {
        
            echo $e->getMessage();exit;
        }
        return $return;
    }
}
