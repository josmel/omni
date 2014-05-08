<?php

class Landing_CartController extends Core_Controller_ActionLanding
{

    public function init() {
        parent::init();
        $this->view->noNav = true;
    }

    public function indexAction() {
        // action body
        $carrito = Store_Cart_Factory::createInstance();
   
        if($this->getRequest()->isPost()){

            $params = $this->getAllParams();
            
            foreach($this->getParam('txtcant', array()) as $key => $value) {
                if($value > 0) $carrito->updateQuantity($key, $value, true);
                else $carrito->remove($key);
            }            
            if ($carrito->getProducts()->count() <= 0) 
                $this->_flashMessenger->warning('El carrito está vacío', 'TEMP');
            else {
                //$this->_flashMessenger->success("El carrito fue validado correctamente");
                $carrito->setStep(2);
                $this->redirect('cart/account-validate');
            }
        }
        
        $subTotal = $carrito->getTotal();
        $igv = $subTotal * $this->_businessman['iva'];
        $total = $subTotal + $igv;
        
        $this->view->itemList = $carrito->getProducts()->getIterator();
        $this->view->subTotal = number_format($subTotal, 2, '.', ' ');
        $this->view->igvTotal = number_format($igv, 2, '.', ' ');
        $this->view->total = number_format($total, 2, '.', ' ');
        $this->view->cartStep = 2;
        //var_dump($carrito->getProducts()->getIterator()); 
    }
    
    public function accountValidateAction() { 
        // action body
        $this->view->cartStep = 3;

        $carrito = Store_Cart_Factory::createInstance();
        $this->validateStep(2, $carrito);
        if ($carrito->getProducts()->count() <= 0) $this->redirect('cart/');
        
        $hasLogin = false;
        
        if (Zend_Auth::getInstance()->hasIdentity()) 
            $hasLogin = true;
        
        $mAddress = new Businessman_Model_ShipAddress();
        
        $formJoined = new Businessman_Form_Joined();
        Application_Form_FormBase_FormCaptcha::elementCaptcha($formJoined);
        
        $formJoined->addElement('hash', 'tokenHash', array('salt' => 'Solicitud no válida.'));
        $formJoined->setDecorators(array(array('ViewScript',
            array('viewScript'=>'forms/_formJoined.phtml'))));
        $formJoined->setState('new');
        
        
        $formAddress = new Businessman_Form_ShipAddress();
        $formAddress->setCountry($this->_businessman['codpais'], $this->_businessman['nompais']);
        $formAddress->addElement('hash', 'tokenHash', array('salt' => 'Solicitud no válida.'));
        $formAddress->setDecorators(array(array('ViewScript',
            array('viewScript'=>'forms/_formShipAddress.phtml'))));
        $accountView = 1;
        
        if ($this->getRequest()->isPost()) {
            $params = $this->getAllParams();
            //var_dump($params);
            
            if (isset($params['submitLogin'])) {
                $user = $params['txtUsername'];
                $pass = $params['txtPass'];
                $pass = md5($pass);
                            
                $login = $this->auth($user,$pass);
                if ($login){        
                    $hasLogin = true;
                    $msg = "Bienvenido ".$this->_joined->name." ".$this->_joined->lastname;
                    $this->_flashMessenger->success($msg);
                    $this->redirect('cart/account-validate');
                } else {
                    $msg = "No se logró iniciar sesión. Verifique que su usuario y clave sean los correctos.";
                    $this->_flashMessenger->error($msg, 'TEMP');
                }
            } elseif (isset($params['submitRegisterJoined'])) {
                $accountView = 2;
                if($formJoined->isValid($params)) {
                    $modelJoined = new Businessman_Model_Joined();
                    $params['password'] = md5($params['password']);
                    $params['codempr'] = $this->_businessman['codempr'];
                    
                    $date = new Zend_Date($params['birthdate'], 'dd/MM/y');
                    $params['birthdate'] = $date->get(
                            Zend_Date::YEAR.'-'.Zend_Date::MONTH.'-'.Zend_Date::DAY
                        );
                    $modelJoined->insert($params);
                    
                    $login = $this->auth($params['email'], $params['password']);
                    
                    if ($login){        
                        $hasLogin = true;
                        $this->_flashMessenger->success("El registró se realizo correctamente.", 'TEMP');
                        $this->redirect('cart/account-validate');
                    } else {
                        $msg = "Ocurrió un error al realizar el registro.";
                        $this->_flashMessenger->error($msg, 'TEMP');
                    }
                    
                } else {
                    //var_dump($formJoined->getErrors()); exit;
                    $errorMsgs = Core_FormErrorMessage::getErrors($formJoined);
                    $this->_flashMessenger->error($errorMsgs, 'TEMP');
                }
            } elseif (isset($params['submitNext'])) {
                if (Zend_Auth::getInstance()->hasIdentity()) {
                    $mUbigeo = new Businessman_Model_Ubigeo();
                    $idAddress = $params['idaddress'];
                    
                    if ($params['newAddress'] == '1') {
                        $formAddress->getElement('city')->setMultiOptions(
                                $mUbigeo->findAllByCountryPairs(
                                        $this->_businessman['codpais'], 
                                        $params['state']
                                )
                            );
                        $formAddress->getElement('district')->setMultiOptions(
                                $mUbigeo->findAllByCountryPairs(
                                        $this->_businessman['codpais'], 
                                        $params['city']
                                )
                            );
                        if($formAddress->isValid($params)) {
                            $params['codpais'] = $this->_businessman['codpais'];
                            $params['codubig'] = $params['district'];
                            $params['idcliper'] = $this->_joined->idcliper;
                            
                            $idAddress = $mAddress->insert($params);
                        } else {
                            $errorMsgs = Core_FormErrorMessage::getErrors($formAddress);
                            $this->_flashMessenger->error($errorMsgs, 'TEMP');
                            //var_dump($errorMsgs);
                        }
                    } 
                    
                    if ($idAddress != '-1') {
                        $address = array();
                        $ubigeo = array();
                        $address = $mAddress->findById($idAddress);
                        $ubigeo = $mUbigeo->findById($address['codpais'], $address['codubig']);                        
                        if($ubigeo['ivaubig'] != $this->_businessman['iva']) {
                            $this->_flashMessenger->info(
                                'El Impuesto ha variado para su distrito de entrega.'
                                .'El ubigeo es <b>'.$ubigeo['ivaubig'].'</b>'
                            );
                            $carrito->setIva($ubigeo['ivaubig']);
                        } else {
                            $carrito->setIva($this->_businessman['iva']);
                        }
                        //var_dump($ubigeo); exit;
                    }
                    
                    $carrito->setIdAddress($idAddress);
                    $carrito->setDollarFactor($this->_businessman['dolpais']);
                    $carrito->setCurrencySimbol($this->_businessman['simbolo']);
                    $carrito->setCurrencyDolarFactor($this->_businessman['dolpais']);
                    $carrito->setCurrencyCode($this->_businessman['codint']);
                    $carrito->setDataBusinessman($this->_businessman);
                    
                    $carrito->setStep(3);
                    $this->redirect('cart/payment');
                } 
            }
        }
        
        $selectAdresses = new Zend_Form_Element_Select('idaddress');
        $selectAdresses->setAttrib('class', 'select-large');
        $dataAddresses = array('-1' => 'Entrega en tienda');
                
        if($hasLogin) {
            $dataAddresses = 
                    $mAddress->findAllByJoinedPairs(
                            $this->_joined->idcliper, 
                            $this->_businessman['codpais']
                    );
        }
        
        $selectAdresses->setMultiOptions($dataAddresses);
        $this->view->selectAdresses = $selectAdresses;
        
        $this->view->formJoined = $formJoined;
        $this->view->formAddress = $formAddress;
        
        $this->view->hasLogin =  $hasLogin;
        $this->view->accountView =  $accountView;
    }
    
    public function paymentAction() {
        // action body
        $carrito = Store_Cart_Factory::createInstance();  
        $this->validateStep(3, $carrito);
        
        $this->view->cartStep = 4;
        $idOrder = $carrito->getIdOrder();
        $carrito->setDoc($this->_joined->ndoc);
        $carrito->setVoucherType('FAC');
        
        $subTotal = $carrito->getTotal();
        $iva = $carrito->getIva();
        $igvTotal = $subTotal * $iva;
        $shipPrice = $carrito->getShipPrice();
        $total = $subTotal + $igvTotal + $shipPrice;
        $carrito->setTotalOrder($total);    
        
        $formPayment = new Shop_Form_Payment(
                $this->_businessman, 
                number_format($subTotal, 2, '.', ' '),
                number_format($igvTotal, 2, '.', ' '),
                number_format($iva * 100, 2, '.', ' '),
                number_format($shipPrice, 2, '.', ' '),
                number_format($total, 2, '.', ' ')
            );
        $formPayment->addElement('hash', 'tokenHash', array('salt' => 'Solicitud no válida.'));
        $formPayment->addDecoratorCustom('forms/_formPayment.phtml');
        $this->view->formPayment = $formPayment;
    }
            
    public function ajaxAddProductAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true); 
        
        $mProduct = new Shop_Model_Product();
        //Ejemplo de implementacion
        $carrito = Store_Cart_Factory::createInstance();
        
        $dataProduct = $mProduct->getById(
                $this->_getParam('idproduct', -1), 
                $this->_businessman['codpais']
            );
        if (!empty($dataProduct)) {
            $description = $dataProduct['codprod'].'**'.$dataProduct['desprod'];
        
            $pd = new Store_Product(
                    $dataProduct['codprod'], $dataProduct['desprod'], 
                    $dataProduct['shorttext'], $dataProduct['monprec']
                );
            $pd->setSlug($dataProduct['slug']);
            $pd->setPoints($dataProduct['punprod']);
            
            $item = new Store_Cart_Item($pd, 1);
            $carrito->addCart($item);
            //var_dump($carrito->getProducts()->getIterator()); exit;
            $msg = "Se agregó el producto al carrito.";
            $state = 1;
        } else { 
            $msg = "El producto no existe.";
            $state = 0;
        }
        
        $carrito->setStep(1);
        //var_dump($dataProduct); exit;
        
        $return = array(
                'state' => $state, 
                'msg' => $msg, 
                'totalItems' => $carrito->getProducts()->count()
            );
        
        $this->getResponse()            
            ->setHttpResponseCode(200)
            ->setHeader('Content-type', 'application/json; charset=UTF-8', true)
            ->appendBody(json_encode($return));
    }
    
    public function ajaxRemoveProductAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true); 
        
        //Ejemplo de implementacion
        $carrito = Store_Cart_Factory::createInstance();

        //var_dump($dataProduct); exit;
        
        $carrito->remove($this->_getParam('idproduct', 0));
        
        $msg = "Se quitó el producto del carrito.";
        $state = 1;
            
        $return = array(
                'state' => $state, 
                'msg' => $msg, 
                'totalItems' => $carrito->getProducts()->count()
            );
        $carrito->setStep(1);
        $this->getResponse()            
            ->setHttpResponseCode(200)
            ->setHeader('Content-type', 'application/json; charset=UTF-8', true)
            ->appendBody(json_encode($return));
    }      
    
    public function updateCaptchaAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $form=Application_Form_FormBase_FormCaptcha::elementCaptcha();
        $captcha = $form->getElement('captcha')->getCaptcha();
        $data=array();
        $data['id']  = $captcha->generate();   
        $data['src'] = $captcha->getImgUrl().$captcha->getId().$captcha->getSuffix(); 
        $this->getResponse()            
            ->setHttpResponseCode(200)
            ->setHeader('Content-type', 'application/json; charset=UTF-8', true)
            ->appendBody(json_encode($data));
   
    }
    
    public function payAction()
    {         
        $this->view->cartStep = 4;   
        $dataCart = Store_Cart_Factory::createInstance();
        $this->validateStep(3, $dataCart);
        $ok = false;
        $cardCode=$this->getParam('cardcode',0);
        $paymethod=$this->getParam('paymethod',0);
                
        if($this->_request->isPost() && !empty($paymethod)){ 
            $mAddress = new Businessman_Model_ShipAddress();
            $mUbigeo = new Businessman_Model_Ubigeo();
            $orderMailHelper = $this->getHelper('orderMail');
            
            $dataCart->setDataJoined($this->_joined);
            $dataCart->setCardCode($cardCode);     
            $dataCart->setPayMethod($paymethod);     
            
            $idAddress = $dataCart->getIdAddress();
            $address = array();
            $ubigeo = array();
            if ($idAddress == '-1') {
                $ubigeo = $mUbigeo->findById(
                    $this->_businessman['codpais'], 
                    $this->_businessman['codubig']
                );
                $address = 'SHOP';   
                $dataCart->setShipPrice(0);
                $dataCart->setShipType('shop');
            } else {
                $address = $mAddress->findById($idAddress);
                $ubigeo = $mUbigeo->findById($address['codpais'], $address['codubig']);
            }
            
            $orderHelper = $this->getHelper('sendOrder');
            if (!$orderHelper->sendOrder($this->_businessman, $this->_joined, $address, $ubigeo, $dataCart, $this->_config))  {
                $this->_flashMessenger->error("Problema de conexión.");
                $this->redirect('cart/payment');
            }
                    
            $pay = new Core_Pay_Pago($paymethod, $dataCart,$this->_config['pasarela'], $this->_businessman['codpais']);            
            
            if($paymethod!='TP004'){   
                 $pay->pagar(); 
//            $selectorHelper = $this->getHelper('selectorPay');
//            $selectorHelper->payMethod($paymethod,$pay);
                if($pay->isSuccess()) {
                    $address = $mAddress->findByIdExtend($dataCart->getIdAddress(), 
                        $this->_businessman['codpais']);                
                    $orderHelper = $this->getHelper('sendOrder');
                    $orderHelper->sendBill($this->_businessman, $dataCart, $this->_config);                
                    //Enviar email de confirmación de compra exitosa
                    $orderMailHelper->mailOrderComplete($this->_businessman,$this->_joined, 
                        $address,$dataCart,$this->getHelper('mail'));                
                    $this->view->totalCart = $dataCart->getProducts()->count();
                    $dataCart->reset();
                    $ok = true;
                }            
            }else{
                echo $pay->getMensaje();
                exit;

            }
        } else {
             $this->_flashMessenger->warning("No escogió metodo de pago");
             $this->redirect('/cart/payment');
        }
        $this->view->idOrder = $dataCart->getIdOrder();
        $this->view->message = $pay->getMensaje();
        $this->view->ok = $ok;
    }
    
    
    
    public function returnPayAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
    }
    
    public function alignetSuccessAction() {
        //$this->_helper->layout()->disableLayout();
        //$this->_helper->viewRenderer->setNoRender(true);
        $ok = false;
        $dataCart = Store_Cart_Factory::createInstance();
        $cache = Zend_Registry::get('Cache');
        $cacheName = 'order_'.$dataCart->getIdOrder().'_order';
        //var_dump($cacheName);
        $result = $cache->load($cacheName);
        //var_dump($result);
        if ($result && $result['businessman']['codempr'] == $this->_businessman['codempr']
            && $result['joined']->idcliper == $this->_joined->idcliper
            && isset($result['alignetResult'])) {
            $alignetResult = $result['alignetResult'];
            $this->view->message = $alignetResult['message'];
            //var_dump($alignetResult);
            if ($alignetResult['alignetSuccess']) {
                $mAddress = new Businessman_Model_ShipAddress();
                $ok = true;
                $address = $mAddress->findByIdExtend($dataCart->getIdAddress(), 
                    $this->_businessman['codpais']);                
                $orderHelper = $this->getHelper('sendOrder');
                $orderHelper->sendBill($this->_businessman, $dataCart, $this->_config); 

                //Enviar email de confirmación de compra exitosa
                $orderMailHelper = $this->getHelper('orderMail');
                $orderMailHelper->mailOrderComplete($this->_businessman,$this->_joined, 
                    $address,$dataCart,$this->getHelper('mail'));                
                $this->view->totalCart = $dataCart->getProducts()->count();
                $this->view->idOrder = $dataCart->getIdOrder();

                $dataCart->reset();
            }
        }  else {
            $this->view->message = "Error Desconocido"; 
        }
        $this->view->ok = $ok;
    }
    
    public function nextPaySuccessAction() {
        //$this->_helper->layout()->disableLayout();
        //$this->_helper->viewRenderer->setNoRender(true);
        $ok = false;
        $dataCart = Store_Cart_Factory::createInstance();
        $cache = Zend_Registry::get('Cache');
        $cacheName = 'order_'.$dataCart->getIdOrder().'_order';
        //var_dump($cacheName);
        $result = $cache->load($cacheName);
        //var_dump($result);
        if ($result && $result['businessman']['codempr'] == $this->_businessman['codempr']
            && isset($result['nextPayResult'])) {
            $npResult = $result['nextPayResult'];
            
            $state = Core_Service_NextPay::getState($npResult['payment_status']);
             
            $this->view->message = Core_Service_NextPay::getMessage($state);
            
            if ($state == Core_Service_NextPay::APPROVED) {
                $ok = true;
                $mAddress = new Businessman_Model_ShipAddress();
                
                $address = $mAddress->findByIdExtend($dataCart->getIdAddress(), 
                    $this->_businessman['codpais']);                
                $orderHelper = $this->getHelper('sendOrder');
                $orderHelper->sendBill($this->_businessman, $dataCart, $this->_config); 

                //Enviar email de confirmación de compra exitosa
                $orderMailHelper = $this->getHelper('orderMail');
                $orderMailHelper->officeOrderComplete($this->_businessman,
                        $address, $dataCart, $this->getHelper('mail'));               
                $this->view->totalOrder = number_format($dataCart->getTotalOrder(), 2, '.', ' ');
                $this->view->idOrder = $dataCart->getIdOrder();

                $dataCart->reset();
            }
        }  else {
            $this->view->message = "Error Desconocido"; 
        }
        $this->view->ok = $ok;
    }
    
    public function infophpAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);     
        if(APPLICATION_ENV!='production'){ phpinfo ();exit;}else{$this->redirect('/');}
    }
    
    public function alignetAction()
    {
        //$this->_helper->layout()->disableLayout();
        //$this->_helper->viewRenderer->setNoRender(true);
        $dataCart = Store_Cart_Factory::createInstance();
        $dataCart->setDataJoined($this->_joined);
        $dataCart->setCardCode('');                        
        $pay = new Core_Pay_Pago('TP004', $dataCart,$this->_config['pasarela']);   
        $pay->pagar();
        echo $pay->getMensaje();
    }
    
    private function validateStep($step, $dataCart) {
        $processStep = $dataCart->getStep($step);
        if($step == $processStep) return true;
        if($step > 0 && $processStep > $step) {
            $dataCart->setStep($step);
        } else {
            switch ($processStep) {
                case 1: $this->redirect('/cart'); break;
                case 2: $this->redirect('/cart/account-validate'); break;
                case 3: $this->redirect('/cart/payment'); break;
                default : if ($step != 1) $this->redirect('/cart'); break;
            }
        }
    }
}
