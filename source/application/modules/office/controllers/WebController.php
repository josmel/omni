<?php

class Office_WebController extends Core_Controller_ActionOffice {

    public function init() {
        /* Initialize action controller here */
        parent::init();
    }

    public function indexAction() {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect('/');
        }
        $bisenessman = $this->_businessman;
        $form = new Businessman_Form_Businessman();
        if (!empty($bisenessman['codempr'])) {
            $obj = new Application_Entity_RunSql('Businessman');
            $obj->getone = $bisenessman['codempr'];
            $dataObj = $obj->getone;
          $form->populate($dataObj);
        }
        $picture = DINAMIC_URL . "businessman/" . $this->_config['app']['businessman']['picture']['two']
                ['height'] . 'x' . $this->_config['app']['businessman']['picture']['two']['width'] . '/' . $bisenessman['picture'];
        $this->view->picture = $picture;
        $this->view->facebook = $this->_config['network']['businessman']['facebook'];
        $this->view->twitter = $this->_config['network']['businessman']['twitter'];
        $this->view->youtube = $this->_config['network']['businessman']['youtube'];
        $this->view->form = $form;
    }

    public function editAction() {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect('/');
        }
        $this->_helper->viewRenderer->setNoRender(true);
        $form = new Businessman_Form_Businessman();
        $obj = new Application_Entity_RunSql('Businessman');
        $bisenessman = $this->_businessman;
        if ($this->_request->isPost()) {
            $dataForm = $this->_request->getPost();
            try {
                if (!empty($dataForm['codempr'])) {
                    $dataForm['tmsfecmodif'] = date('Y-m-d H:i:s');
                    $dataForm['vchusumodif'] = $dataForm['codempr'];
                    $obj->edit = $dataForm;
                }
                
                $cacheName = $this->_businessman["subdomain"].'_businessman';  
                $cache = Zend_Registry::get('Cache');
                $cache->remove($cacheName);
                $this->auth($this->_businessman["codempr"], $this->_businessman["claempr"]);
                $this->_flashMessenger->success("Datos Actualizados Correctamente.");
                $this->_redirect('/web');
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        } else {
            if (!empty($bisenessman['codempr'])) {
                $obj->getone = $bisenessman['codempr'];
                $dataObj = $obj->getone;
                $form->populate($dataObj);
            }
            $this->view->form = $form;
        }
    }

    public function businessmanPictureAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $form = new Core_Form_Form();
        $i = new Zend_Form_Element_File('picture');
        $form->addElement($i);
        $form->getElement('picture')
                ->setDestination(ROOT_IMG_DINAMIC . '/businessman/origin/')
                ->addValidator('Size', false, 10024000) // limit to 100k
                ->addValidator('Extension', true, 'jpg,png,gif,jpeg')// only JPEG, PNG, and GIFs
                ->setRequired(false);
        if ($this->_request->isPost()) {
            try {
                if ($form->picture->receive()) {
                    $fileName = $form->picture->getFileName();
                    $nombre = "";
                    $code = "";
                    if (!empty($fileName)) {
                        $fInfo = $form->picture->getFileInfo();
                        $nombre = explode('.', $fInfo['picture']['name']);
                        $ext = $nombre[count($nombre) - 1];
                        unset($nombre[count($nombre) - 1]);
                        $nombre = implode('_', $nombre);
                        $code = Core_Utils_Utils::getRamdomChars(15, 'A');
                        $nombre = $code . '.' . $ext;
                        $newName = ROOT_IMG_DINAMIC . "/businessman/origin/" . $nombre;
                        rename($fileName, $newName);
                        $dataForm['picture'] = $nombre;
                        $dataForm['tmsfecmodif'] = date('Y-m-d H:i:s');
                        $dataForm['vchusumodif'] = $this->_businessman["codempr"];
                        $updatePicture = new Businessman_Model_Businessman();
                        $updatePicture->updatePicture($this->_businessman["codempr"], $dataForm);
                        $setBannerHelper = $this->getHelper('SetBusinessmanPicture');
                        $one = array('heigth' => $this->_config['app']['businessman']['picture']['one']['height'],
                            'width' => $this->_config['app']['businessman']['picture']['one']['width']);
                        $two = array('heigth' => $this->_config['app']['businessman']['picture']['two']['height'],
                            'width' => $this->_config['app']['businessman']['picture']['two']['width']);
                        $setBannerHelper->setBusinessman($nombre, $one, $two, $this->_businessman["picture"]);
                        $msg = 'imagen guardada correctamente';
                        $state = 1;
                        $picture = DINAMIC_URL . "/businessman/" . $this->_config['app']['businessman']['picture']['two']
                                ['height'] . 'x' . $this->_config['app']['businessman']['picture']['two']['width'] . '/' . $nombre;

                        $this->auth($this->_businessman["codempr"], $this->_businessman["claempr"]);
                    } else {
                        $msg = 'faltacargar Imagen';
                        $state = 0;
                    }
                }
            } catch (Exception $e) {
                $state = '0';
                $msg = 'Ocurrió un error al subir la imagen.';
            }
        }
        $return = array(
            'state' => $state,
            'msg' => $msg,
            'urlImagen' => $picture
        );
        echo json_encode($return); exit;
    }

    public function ajaxSubdomainAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $subdomain = $this->getParam('subdomain');
        if (!empty($subdomain)) {
            try {
                $bisenessman = $this->_businessman;
                $obj = new Application_Model_DbTable_Businessman();
                $valor = $obj->getSubDomain($subdomain);
                if ($valor['subdomain'] == $bisenessman['subdomain'] or $valor['subdomain'] == null) {
                    $msg = true;
                } else {
                    $msg = false;
                }
            } catch (Exception $e) {
                $msg = $e->getMessage();
            }
        } else {
            $msg = 'faltan datos';
        }
        $this->getResponse()
                ->setHttpResponseCode(200)
                ->setHeader('Content-type', 'application/json; charset=UTF-8', true)
                ->appendBody(json_encode($msg));
    }

    public function updatePassAction() {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect('/');
        }
        $form = new Businessman_Form_UpdateBusinessmanPassword();
        $mBusinessman = new Businessman_Model_Businessman();
        if ($this->_request->isPost()) {
            $params = $this->getAllParams();
            if ($form->isValid($params)) {
                $claBusinessman = md5($params['claempr']);
                if ($claBusinessman == $this->_businessman['claempr']) {
                    if ($params['confirmone'] == $params['confirmtwo']) {
                        $passBusinessman = md5($params['confirmone']);
                        $mBusinessman->updateBusinessmanPass($passBusinessman, $this->_businessman['codempr']);
                        $this->auth($this->_businessman["codempr"], $passBusinessman);
                        $this->_flashMessenger->success("Constraseña Cambiada Correctamente.");
                        $this->_redirect('/web/update-pass');
                    } else {
                        $msg = "Las Nuevas Contraseñas no Coinciden.";
                        $this->_flashMessenger->warning($msg, 'TEMP');
                    }
                } else {
                    $msg = "Contraseña Actual Incorrecta.";
                    $this->_flashMessenger->warning($msg, 'TEMP');
                }
            } else {
                $errorMsgs = Core_FormErrorMessage::getErrors($form);
                $this->_flashMessenger->error($errorMsgs);
            }
        }
        $this->view->form = $form;
    }

    public function detailAction() {
        
    }

    public function getBlogDataAction() {
        $this->redirect('/');
        ini_set ( 'max_execution_time', 120); 
        $url = $this->_config['app']['blog']['url'];
        $archivo = fopen($url, 'r');
        $datos = stream_get_contents($archivo);
        $noticias = simplexml_load_string($datos);
        $valoresBlog[] = array();
        for ($i = 0; $i < count($noticias->channel->item); $i++) {
            foreach ($noticias->channel->item as $noticia) {
                $valoresBlog[$i]['titulo'] = $noticia->title;
                $valoresBlog[$i]['descripcion'] = $noticia->description;
                $valoresBlog[$i]['url'] = $noticia->link;
                $i++;
            }
        }
        $mBlog = new Admin_Model_Blog();
        $mBlog->insertBlogCron($valoresBlog);
        echo 'acabo el cron';
        ini_set('max_execution_time', 30); 
        exit;
    }

}

