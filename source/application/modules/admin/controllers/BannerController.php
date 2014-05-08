<?php

class Admin_BannerController extends Core_Controller_ActionAdmin {

    public function init() {
        parent::init();
    }

    public function indexAction() {
        $mBannerType = new Admin_Model_BannerType();
        $mBanner = new Admin_Model_Banner();
        $bannerTypes = $mBannerType->getPairsAll();
        $bTypeCodes = array_keys($bannerTypes);

        $selectedType = $this->getParam('type', '');
        if (empty($selectedType)) {
            $selectedType = $bTypeCodes[0];
        }

        if ($this->_request->isPost()) {
            //editar, eliminar o agregar item segun sus estados
            $params = $this->getAllParams();
            $bannerType = $mBannerType->findById($selectedType);

            $setBannerHelper = $this->getHelper('SetBannerGroup');
            $setBannerHelper->setBanners(
                    $params, $bannerType, $this->_identity->iduser, $mBanner, new Admin_Model_Image()
            );
        }

        $banners = $mBanner->findAllByType($selectedType);
        if ($selectedType == 'HOMEOFVI') {
            $mensaje = 'Las imágenes deben tener un máximo de 604 pixeles por 300 pixeles';
        } elseif ($selectedType == 'HALLFAME') {
            $mensaje = 'Las imágenes deben tener un máximo de 300 pixeles por 570 pixeles';
        } elseif ($selectedType == 'LANDPORT') {
            $mensaje = 'Las imágenes deben tener un máximo de 486 pixeles por 272 pixeles';
        }
        $this->addYosonVar('bannerType', SITE_URL . 'banner/type/');
        $this->view->types = $bannerTypes;
        $this->view->selectedType = $selectedType;
        $this->view->banners = $banners;
        $this->view->mensaje = $mensaje;
    }

    public function listAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $params = $this->_getAllParams();
        $iDisplayStart = isset($params['iDisplayStart']) ? $params['iDisplayStart'] : null;
        $iDisplayLength = isset($params['iDisplayLength']) ? $params['iDisplayLength'] : null;
        $sEcho = isset($params['sEcho']) ? $params['sEcho'] : 1;
        $sSearch = isset($params['sSearch']) ? $params['sSearch'] : '';
        $obj = new Application_Entity_DataTable('Banner', 10, $sEcho, false);
        $obj->setIconAction($this->action());
        $query = !empty($sSearch) ? " AND titulo like '%" . $sSearch . "%'" : " ";
        $obj->setSearch($query);



        $this->getResponse()
                ->setHttpResponseCode(200)
                ->setHeader('Content-type', 'application/json;charset=UTF-8', true)
                ->appendBody(json_encode($obj->getQuery($iDisplayStart, $iDisplayLength)));
    }

    public function bannerImageAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $form = new Core_Form_Form();

        $i = new Zend_Form_Element_File('imagen');
        $form->addElement($i);
        $form->getElement('imagen')
                ->setDestination(ROOT_IMG_DINAMIC . '/banner/origin/')
                ->addValidator('Size', false, 10024000) // limit to 100k
                ->addValidator('Extension', true, 'jpg,png,gif,jpeg')// only JPEG, PNG, and GIFs
                ->setRequired(false);

        $rpta = array();
        if ($this->_request->isPost()) {
            try {
                if ($form->imagen->receive()) {
                    $fileName = $form->imagen->getFileName();
                    $nombre = "";
                    $code = "";
                    if (!empty($fileName)) {
                        $fInfo = $form->imagen->getFileInfo();
                        $nombre = explode('.', $fInfo['imagen']['name']);
                        $ext = $nombre[count($nombre) - 1];
                        unset($nombre[count($nombre) - 1]);
                        $nombre = implode('_', $nombre);
                        $code = Core_Utils_Utils::getRamdomChars(15, 'A');
                        $nombre = $code . '.' . $ext;
                        $newName = ROOT_IMG_DINAMIC . "/banner/origin/" . $nombre;
                        rename($fileName, $newName);
                    }

                    $rpta['state'] = '1';
                    $rpta['msg'] = 'Ok';
                    $rpta['code'] = $code;
                    $rpta['nombre'] = $nombre;
                    $rpta['source'] = DINAMIC_URL . "banner/origin/" . $nombre;
                }
            } catch (Exception $e) {
                $rpta['state'] = '0';
                $rpta['msg'] = 'Ocurrió un error al subir la imagen.';
            }
        }

        echo json_encode($rpta);
        exit;
    }

    public function newAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $form = new Admin_Form_Banner();
        $obj = new Application_Entity_RunSql('Banner');
        if ($this->_request->isPost()) {

            $dataForm = $this->_request->getPost();
            $mBannerType = new Admin_Model_BannerType();
            try {

                $msj = array();
                var_dump($dataForm);
                $selectedType = $this->getParam('type', '');
                $bannerType = $mBannerType->findById($selectedType);
                if (!$form->nombre->receive()) {
                    $msj[] = $form->getMessages();
                } else {
                    $mImage = new Admin_Model_Image();
                    $fileName = $form->nombre->getFileName();
                    if (!empty($fileName)) {
                        $fInfo = $form->nombre->getFileInfo();
                        $nombre = explode('.', $fInfo['nombre']['name']);
                        $ext = $nombre[count($nombre) - 1];
                        unset($nombre[count($nombre) - 1]);
                        $nombre = implode('_', $nombre);

                        $dataForm['nombre'] = str_replace(" ", "_", substr($nombre, 0, 30))
                                . '_' . Core_Utils_Utils::getRamdomChars(5) . '.' . $ext;

                        $resize = new Core_Utils_ResizeImage($form->nombre->getFileName());

                        $resize->resizeImage($bannerType['anchoimg'], $bannerType['altoimg'], 'exact');
                        $resize->saveImage(
                                ROOT_IMG_DINAMIC . '/banner/' . $bannerType['codproy']
                                . '/' . $bannerType['anchoimg'] . 'x'
                                . $bannerType['altoimg'] . '/' . $dataForm['nombre']
                        );
                    }
                    $image = array(
                        'nombre' => $data['nombre'],
                        'vchestado' => 1,
                        'vchusucrea' => $this->_identity->iduser
                    );
                    $dataForm['idimagen'] = $mImage->insert($image);
                }

                if (empty($dataForm['idbanner'])) {
                    $dataForm['tmsfeccrea'] = date('Y-m-d H:i:s');
                    $dataForm['vchusucrea'] = $this->_identity->iduser;
                    $obj->save = $dataForm;
                } else {
                    $dataForm['tmsfecmodif'] = date('Y-m-d H:i:s');
                    $dataForm['vchusumodif'] = $this->_identity->iduser;
                    //var_dump($dataForm); return;
                    $obj->edit = $dataForm;
                }
                $this->_redirect('/banner');
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        } else {
            $this->view->titulo = "Nuevo Banner";
            $this->view->submit = "Grabar Banner";
            $this->view->action = "/banner/new";
            $form->addDecoratorCustom('forms/_formBanner.phtml');
            echo $form;
        }
    }

    public function editAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_getParam('id', 0);
        $form = new Admin_Form_Banner();
        if (!empty($id)) {
            $obj = new Application_Entity_RunSql('Banner');
            $obj->getone = $id;
            $dataObj = $obj->getone;
            $form->populate($dataObj);
        }
        $this->view->titulo = "Editar Banner";
        $this->view->submit = "Guardar cambios";
        $this->view->action = "/banner/new";
        $form->setDecorators(array(array('ViewScript',
                array('viewScript' => 'forms/_formBanner.phtml'))));
        echo $form;
    }

    public function deleteAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->getParam('id');
        $rpta = array();
        if (!empty($id)) {
            try {
                $obj = new Application_Entity_RunSql('User');
                $obj->edit = array('state' => 'I', $obj->getPK() => $id);
                $rpta['msj'] = 'ok';
            } catch (Exception $e) {
                $rpta['msj'] = $e->getMessage();
            }
        } else {
            $rpta['msj'] = 'faltan datos';
        }
        $this->getResponse()
                ->setHttpResponseCode(200)
                ->setHeader('Content-type', 'application/json; charset=UTF-8', true)
                ->appendBody(json_encode($rpta));
    }

    function action() {
        $action = "<a class=\"tblaction ico-edit\" title=\"Editar\" href=\"/banner/edit/id/__ID__\">Editar</a>
                    <a data-id=\"__ID__\" class=\"tblaction ico-delete\" title=\"Eliminar\"  href=\"/banner/delete\">Eliminar</a>";
        return $action;
    }

}

