<?php

class Landing_AccountController extends Core_Controller_ActionLanding {
    
    public function init() {
        parent::init();
        $this->view->noNav = true;
    }

    public function indexAction() {
        if (Zend_Auth::getInstance()->hasIdentity()) 
            $this->_redirect('/account/my-account');
        if ($this->_request->isPost()) {
        }
    }
    
    public function myAccountAction() {
        if (!Zend_Auth::getInstance()->hasIdentity()) 
            $this->_redirect('/account');
        
        $form = new Businessman_Form_Joined();
        $form->addElement('hash', 'tokenHash', array('salt' => 'Solicitud no válida.'));
        $form->setState('edit');
        Application_Form_FormBase_FormCaptcha::elementCaptcha($form);
        if ($this->_request->isPost()) {
            //var_dump($this->getAllParams()); exit;
            $params = $this->getAllParams();
            if($form->isValid($params)) {
                $idJoined = $params['idcliper'];
                if ($idJoined != $this->_joined->idcliper) { 
                    //intento de suplantacion
                    return;
                }
                
                $modelJoined = new Businessman_Model_Joined();
                if (trim($params['password']) == '') {
                    $params['password'] = $this->_joined->password;
                } else {
                    $params['password'] = md5($params['password']);
                }
                
                $date = new Zend_Date($params['birthdate'], 'dd/MM/y');
                $params['birthdate'] = $date->get(
                        Zend_Date::YEAR.'-'.Zend_Date::MONTH.'-'.Zend_Date::DAY
                    );
                $modelJoined->update($idJoined, $params);
                
                
                Zend_Auth::getInstance()->clearIdentity();
                $login = $this->auth($params['email'], $params['password']);
                if(!$login) {
                    $this->_flashMessenger->error("Ocurrió un error al realizar la actualizacion.");
                    $this->_redirect('/account');
                } else {
                    $this->_flashMessenger->success("Sus datos se actualizaron correctamente.");
                }
            } else {
                 $errorMsgs = Core_FormErrorMessage::getErrors($form);
                 $this->_flashMessenger->error($errorMsgs);
            }
        } 
        
        $dataAccount = (array) Zend_Auth::getInstance()->getIdentity();
        $date = new Zend_Date(
                $dataAccount['birthdate'], 
                Zend_Date::YEAR.'-'.Zend_Date::MONTH.'-'.Zend_Date::DAY
            );
        $dataAccount['birthdate'] = $date->get(
                Zend_Date::DAY.'/'.Zend_Date::MONTH.'/'.Zend_Date::YEAR
            );
        $form->populate($dataAccount);
        
        $form->setDecorators(array(array('ViewScript',
            array('viewScript'=>'forms/_formJoined.phtml')))); 
        $this->view->form = $form;
    }
    
    public function orderListAction() {
        if (!Zend_Auth::getInstance()->hasIdentity()) 
            $this->_redirect('/account');
        $mOrder = new Shop_Model_Order();
        $orders = $mOrder->findAllByJoined($this->_joined->idcliper);
        
        $this->view->orderList = $orders;
    }
    
    public function forgotPasswordAction() {
        if (Zend_Auth::getInstance()->hasIdentity()) 
            $this->_redirect('/account/my-account');
        
        $sendOk = false;
        
        $form = new Businessman_Form_SendPassword();
        $form->addElement('hash', 'tokenHash', array('salt' => 'Solicitud no válida.'));
        Application_Form_FormBase_FormCaptcha::elementCaptcha($form);
        if ($this->_request->isPost()) {
            $params = $this->getAllParams();
            if($form->isValid($params)) {
                $mJoined = new Businessman_Model_Joined();
                $token = $params['email'].' '.mt_rand(0, mt_getrandmax());
                $token = md5($token);
                $params['token'] = $token;
                
                $joined = $mJoined->findByMail($params['email'], $this->_businessman['codempr']);
                
                if (empty($joined)) {
                    $msg = "El e-mail ingresado no se encuentra registrado.";
                    $this->_flashMessenger->warning($msg, 'TEMP');
                } else {
                
                    $mRecoverAccount = new Businessman_Model_RecoverAccount();
                    $params['idcliper'] = $joined['idcliper'];

                    $mRecoverAccount->insert($params);

                    $mailHelper = $this->getHelper('mail');

                    $sendMail = array(
                        'email' => $params['email'],
                        'name' => $joined['name'],
                        'lastname' => $joined['lastname'],
                        'imgPass' => STATIC_URL.'img/contrasena.gif',
                        'imgFuxion' => STATIC_URL.'img/fuxion.gif',
                        'link' => 'http://'.$_SERVER['HTTP_HOST']
                                    .$this->view->url(
                                            array('token' => $params['token']),
                                            'changePassword'
                                        )
                    );

                    $dataMailing = array(
                        'to' => $params['email'],
                        'data' => Zend_Json_Encoder::encode($sendMail)
                    );

                    $sendMail['dataMailing'] = $dataMailing;
                    $sendMail['to'] = $params['email'];
                    //$sendMail['to'] = 'yrvingrl520@gmail.com';

                    $mailHelper->forgotPassword($sendMail);
                    $this->_flashMessenger->success("Se envió a su correo el link para su cambio de clave.");
                    $sendOk = true;
                }
                //var_dump($params);
            } else {
                $errorMsgs = Core_FormErrorMessage::getErrors($form);
                $this->_flashMessenger->error($errorMsgs);
            }
        }
        
        $form->setDecorators(array(array('ViewScript',
            array('viewScript'=>'forms/_formSendPassword.phtml')))); 
        
        $this->view->sendOk = $sendOk;
        $this->view->form = $form;
    }
    
    public function changePasswordAction() {
        if (Zend_Auth::getInstance()->hasIdentity()) 
            $this->_redirect('/account/my-account');
        $changeOk = false;
        
        $form = new Businessman_Form_ChangePassword();
        $form->addElement('hash', 'tokenHash', array('salt' => 'Solicitud no válida.'));
        
        $mRecoverAccount = new Businessman_Model_RecoverAccount();
        $mJoined = new Businessman_Model_Joined();
        
        if(!$this->_hasParam('token')) $this->_redirect('/account');
        
        $dataRecover = $mRecoverAccount->getValidToken($this->_getParam('token', 'none'));
        
        //var_dump($dataRecover);
        
        if(!$dataRecover) $this->_redirect('/account');
        
        $joined = $mJoined->findById($dataRecover['idcliper'], $this->_businessman['codempr']);
        
        if(empty($joined)) $this->_redirect('/account');
                    
        //var_dump($joined);
        
        if ($this->_request->isPost()) {
            $params = $this->getAllParams();
            if($form->isValid($params)) {
                $params['password'] = md5($params['password']);
                $mJoined->update($joined['idcliper'], $params);
                $mRecoverAccount->downTokenState($joined['idcliper']);
                
                $this->_flashMessenger->success("El cambio de clave se realizo correctamente.");
                //cambio clave
                $changeOk = true;
            } else {
                $errorMsgs = Core_FormErrorMessage::getErrors($form);
                $this->_flashMessenger->error($errorMsgs);
            }
        }
        
        $form->setDecorators(array(array('ViewScript',
            array('viewScript'=>'forms/_formChangePassword.phtml')))); 
        $this->view->changeOk = $changeOk;
        $this->view->form = $form;
    }
    
    public function loginAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $user=$this->_getParam('txtUsername',"");
        $pass=$this->_getParam('txtPass',"");
        $pass=md5($pass);
        if ($this->_request->isPost() && $user!="" && $pass!=""){            
            $login = $this->auth($user,$pass);
            if($login){        
                $this->_redirect('/account/my-account');
            }else{
                $this->_redirect('/account');
            }
        }else{
            $this->_redirect('/account');
        }
    }
    
    public function logoutAction() {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_redirect('/');
    }
    
    public function availableEmailAction(){
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $email = $this->getParam('email', '');
        $mJoined = new Businessman_Model_Joined();
        
        $result = $mJoined->existsMail($email) ? "0" : "1";
        
        $this->getResponse()            
            ->setHttpResponseCode(200)
            ->setHeader('Content-type', 'application/json; charset=UTF-8', true)
            ->appendBody($result);
   
    }
}

