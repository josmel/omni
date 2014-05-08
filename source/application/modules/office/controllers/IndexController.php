<?php

class Office_IndexController extends Core_Controller_ActionOffice
{

    public function init() {
        /* Initialize action controller here */
        parent::init();
    }

    public function indexAction() {
         if(Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect('/home');
        }
    }
    
    public function testAction(){
        if(Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect('/home');
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
                $this->_redirect('/home');
            }else{
                $this->_redirect('/index');
            }
        }else{
          
            $this->_redirect('/index');
        }
    }
      
    
    
    public function forgotPasswordAction() {
        if(Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect('/web');
        }
        $sendOk = false;
        $form = new Businessman_Form_BusinessmanPassword();
        $form->addElement('hash', 'tokenHash', array('salt' => 'Solicitud no válida.'));
        Application_Form_FormBase_FormCaptcha::elementCaptcha($form);
        if ($this->_request->isPost()) {
            $params = $this->getAllParams();
            if($form->isValid($params)) {
                $mBusinessman = new Businessman_Model_Businessman();
                $token = $params['emaempr'].' '.mt_rand(0, mt_getrandmax());
                $token = md5($token);
                $params['token'] = $token;
                $Businessman = $mBusinessman->findByMail($params['emaempr']);
                if (empty($Businessman)) {
                    $msg = "El e-mail ingresado no se encuentra registrado.";
                    $this->_flashMessenger->warning($msg, 'TEMP');
                } else {
                    $params['codempr'] = $Businessman['codempr'];
                    $mBusinessman->insertToken($params);
                    $mailHelper = $this->getHelper('mail');
                    $sendMail = array(
                        'email' => $params['emaempr'],
                        'name' => $Businessman['nomempr'],
                        'lastname' => $Businessman['appempr'].' '.$Businessman['apmempr'],
                        'imgPass' => STATIC_URL.'img/contrasena.gif',
                        'imgFuxion' => STATIC_URL.'img/fuxion.gif',
                        'link' => 'http://'.$_SERVER['HTTP_HOST']
                                    .$this->view->url(
                                            array('token' => $params['token']),
                                            'changePassword'
                                        )
                    );

                    $dataMailing = array(
                        'to' => $params['emaempr'],
                        'data' => Zend_Json_Encoder::encode($sendMail)
                    );
                    $sendMail['dataMailing'] = $dataMailing;
                    $sendMail['to'] = $params['emaempr'];
                    $mailHelper->forgotPassword($sendMail);
                    $this->_flashMessenger->success("Se envió a su correo el link para su cambio de clave.");
                    $sendOk = true;
                }
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
    
      public function changePasswordAction() {
        if(Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect('/web');
        }
        $changeOk = false;
        $form = new Businessman_Form_ChangeBusinessmanPassword();
        $form->addElement('hash', 'tokenHash', array('salt' => 'Solicitud no válida.'));
        $mBusinessman = new Businessman_Model_Businessman();
        if(!$this->_hasParam('token')) $this->_redirect('/');
        $dataRecover = $mBusinessman->getValidToken($this->_getParam('token', 'none'));
        if(!$dataRecover) $this->_redirect('/');
        $Businessman = $mBusinessman->findById($dataRecover['codempr']);
        if(empty($Businessman)) $this->_redirect('/');
                    
        if ($this->_request->isPost()) {
            $params = $this->getAllParams();
            if($form->isValid($params)) {
                $params['claempr'] = md5($params['claempr']);
                $mBusinessman->update($Businessman['codempr'], $params);
                $this->_flashMessenger->success("El cambio de clave se realizo correctamente.");
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
    
    
    public function logoutAction() {
        $dataCart = Store_Cart_Factory::createInstance();
        $dataCart->reset();
        
        $mSession = new Businessman_Model_BusinessmanSession();
        $mSession->sessionFinished($this->_businessman['codempr']);
        
        Zend_Auth::getInstance()->clearIdentity();
        $this->_redirect('/');
    }
}

