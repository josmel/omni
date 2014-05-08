<?php

class Landing_ContactController extends Core_Controller_ActionLanding {

    public function init() {
        parent::init();
    }

    public function indexAction() {
        if($this->getRequest()->isPost()) {
            
            $mailHelper = $this->getHelper('mail');
            //var_dump($helper); exit;
            $params = $this->getAllParams();
            
            $sendMail = array(
                'codempr' => $this->_businessman['codempr'],
                'message' => $params['txaMessage'],
                'email' => $params['txtEmail'],
                'name' => $params['txtName'],
                'lastname' => $params['txtLastName'],
                'bm_name' => $this->_businessman['nomempr'],
                'bm_lastname' => $this->_businessman['appempr'],
                'imgFuxion' => STATIC_URL.'img/fuxion.gif'
            );
                    
            $dataMailing = array(
                'to' => $this->_businessman['emaempr'],
                //'to' => 'onlinesp.pruebas@gmail.com',
                'data' => Zend_Json_Encoder::encode($sendMail)
            );
            
            $sendMail['dataMailing'] = $dataMailing;
            $sendMail['to'] = $this->_businessman['emaempr'];
            //$sendMail['to'] = 'onlinesp.pruebas@gmail.com';
                    
            $mailHelper->contactBusinessman($sendMail);
            $this->_flashMessenger->success("Su solicitud fue enviada.");
            $this->redirect('/#contact');
        }
    }

}
