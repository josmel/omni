<?php

class Admin_FileController extends Core_Controller_ActionAdmin {

    private $_validExtensions = array('pdf', 'doc', 'zip', 'xls', 'png', 'jpg', 'gif', 'jpeg');

    public function init() {
        parent::init();
    }

    public function indexAction() {
        
    }

    public function listAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $params = $this->_getAllParams();
        $iDisplayStart = isset($params['iDisplayStart']) ? $params['iDisplayStart'] : null;
        $iDisplayLength = isset($params['iDisplayLength']) ? $params['iDisplayLength'] : 0;
        $sEcho = isset($params['sEcho']) ? $params['sEcho'] : 1;
        $sSearch = isset($params['sSearch']) ? $params['sSearch'] : '';
        $obj = new Application_Entity_DataTable('File', $iDisplayLength, $sEcho, true);
        $obj->setIconAction($this->action());
        $query = "";
        $query.=!empty($sSearch) ? " AND titulo LIKE '%" . $sSearch . "%' " : " ";
        $obj->setSearch($query);

        $this->getResponse()
                ->setHttpResponseCode(200)
                ->setHeader('Content-type', 'application/json;charset=UTF-8', true)
                ->appendBody(json_encode($obj->getQuery($iDisplayStart, $iDisplayLength)));
    }

    public function validarFileAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $codtfile = $this->getParam('codtfile');
        if (!empty($codtfile)) {
            try {
                $mFileType = new Admin_Model_File();
                $cantidadFile = $mFileType->countFile($codtfile);
                if ($cantidadFile[0]['codproy'] == 'LANDI') {

                        if ($cantidadFile[0]['cantidad'] < 3) {
                            $jsonDatos = array('msg' => 'datos completos', 'state' => 1, 'flag' => 'activo');
                        } else {
                            $jsonDatos = array('msg' => 'datos completos', 'state' => 1, 'flag' => 'inactivo');
                        }
                  
                } else {
                    $jsonDatos = array('msg' => 'datos completos', 'state' => 1, 'flag' => 'activo');
                }
            } catch (Exception $e) {
                $msg = $e->getMessage();
                $jsonDatos = array('msg' => $msg, 'state' => 0);
            }
        } else {
            $jsonDatos = array('msg' => 'faltan datos', 'state' => 0);
        }
        $this->getResponse()
                ->setHttpResponseCode(200)
                ->setHeader('Content-type', 'application/json; charset=UTF-8', true)
                ->appendBody(json_encode($jsonDatos));
    }

    public function newAction() {

        $this->_helper->viewRenderer->setNoRender(true);
        $form = new Admin_Form_File();
        $obj = new Application_Entity_RunSql('File');
        if ($this->_request->isPost()) {
            $dataForm = $this->getAllParams();
            //var_dump($dataForm); exit;
            $mFileType = new Admin_Model_FileType();

            try {
                $fileType = $mFileType->findById($dataForm['codtfile']);
                $msj = array();

                if (!$form->nombre->receive()) {
                    $msj[] = $form->getMessages();
                    if (empty($dataForm['idfile'])) {
                        echo "Debe Adjuntar un archivo válido.";
                        exit;
                    }
                } else {
                    $fileName = $form->nombre->getFileName();
                    if (!empty($fileName)) {
                        $fInfo = $form->nombre->getFileInfo();
                        //var_dump($fInfo);
                        $nombre = explode('.', $fInfo['nombre']['name']);
                        $ext = $nombre[count($nombre) - 1];
                        $code = Core_Utils_Utils::getRamdomChars(15);
                        $keys = array_keys($this->_validExtensions, $ext);
                        //var_dump($keys); exit;
                        if (empty($keys)) {
                            echo "Debe Adjuntar un archivo válido.";
                            exit;
                        }

                        $nombre = $code . '.' . $ext;
                        $dataForm['nombre'] = $nombre;
                        $dataForm['extfile'] = $ext;

                        $destinyFolder = ROOT_IMG_DINAMIC . "/file/" .
                                $fileType['codproy'];
                        if (!file_exists($destinyFolder))
                            mkdir($destinyFolder, 0777, true);

                        $newName = $destinyFolder . '/' . $nombre;
                        rename($fileName, $newName);
                    }
                }
                // var_dump($form->nombre->receive()); exit;
                // var_dump($dataForm); exit;
                if (empty($dataForm['idfile'])) {
                    $dataForm['tmsfeccrea'] = date('Y-m-d H:i:s');
                    $dataForm['vchusucrea'] = $this->_identity->iduser;
                    $obj->save = $dataForm;
                } else {
                    $dataForm['tmsfecmodif'] = date('Y-m-d H:i:s');
                    $dataForm['vchusumodif'] = $this->_identity->iduser;
                    //var_dump($dataForm); return;
                    $obj->edit = $dataForm;
                }
                $this->_redirect('/file');
            } catch (Exception $e) {
                echo "Ocurrió un error.";
                exit;
            }
        } else {
            $this->view->titulo = "Nuevo Archivo";
            $this->view->submit = "Grabar Archivo";
            $this->view->action = "/file/new";
            $form->addDecoratorCustom('forms/_formFile.phtml');
            echo $form;
        }
    }

    public function editAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_getParam('id', 0);
        $form = new Admin_Form_File();
        if (!empty($id)) {
         
            $obj = new Application_Entity_RunSql('File');
            $obj->getone = $id;
            $dataObj = $obj->getone;
            $form->populate($dataObj);
        }
             $mFileType = new Admin_Model_File();
          $cantidadFile = $mFileType->getCodId($id);
        $state= $cantidadFile['vchestado'] == 'A' ? 1 : 0;
        $this->addYosonVar('stFile', $state,false);
        $this->addYosonVar('codFile', $cantidadFile['codtfile']);
        $this->view->titulo = "Editar Archivo";
        $this->view->submit = "Guardar Archivo";
        $this->view->action = "/file/new";
        $form->setDecorators(array(array('ViewScript',
                array('viewScript' => 'forms/_formFile.phtml'))));
        echo $form;
    }

    public function deleteAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->getParam('id');
        $rpta = array();
        if (!empty($id)) {
            try {
                $obj = new Application_Entity_RunSql('File');
                $obj->edit = array('vchestado' => 'D', $obj->getPK() => $id);
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
        $action = "<a class=\"tblaction ico-edit\" title=\"Editar\" href=\"/file/edit/id/__ID__\">Editar</a>
                    <a data-id=\"__ID__\" class=\"tblaction ico-delete\" title=\"Eliminar\"  href=\"/file/delete\">Eliminar</a>";
        return $action;
    }

}

