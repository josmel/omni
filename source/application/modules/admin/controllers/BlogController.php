<?php

class Admin_BlogController extends Core_Controller_ActionAdmin {

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
        $iDisplayLength = isset($params['iDisplayLength']) ? $params['iDisplayLength'] : null;
        $sEcho = isset($params['sEcho']) ? $params['sEcho'] : 1;
        $sSearch = isset($params['sSearch']) ? $params['sSearch'] : '';
        $obj = new Application_Entity_DataTable('Blog', 0, $sEcho, false);
        $obj->setIconAction($this->action());
        $query = "";
        $query.=!empty($sSearch) ? " AND titulo like '%" . $sSearch . "%' " : " ";
        $obj->setSearch($query);
        $this->getResponse()
                ->setHttpResponseCode(200)
                ->setHeader('Content-type', 'application/json;charset=UTF-8', true)
                ->appendBody(json_encode($obj->getQuery($iDisplayStart, $iDisplayLength)));
    }

    public function newAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $obj = new Application_Entity_RunSql('Blog');
        if ($this->_request->isPost()) {
            $dataForm = $this->_request->getPost();
            try {
                $msj = array();
                $dataForm['tmsfecmodif'] = date('Y-m-d H:i:s');
                $dataForm['vchusumodif'] = $this->_identity->iduser;
                $obj->edit = $dataForm;
                $this->_redirect('/blog');
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
    }

    public function editAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_getParam('id', 0);
        $form = new Admin_Form_Blog();
        if (!empty($id)) {
            $obj = new Application_Entity_RunSql('Blog');
            $obj->getone = $id;
            $dataObj = $obj->getone;
            $form->populate($dataObj);
        }
        $this->view->titulo = "Editar Blog";
        $this->view->submit = "Guardar Blog";
        $this->view->action = "/blog/new";
        $form->setDecorators(array(array('ViewScript',
                array('viewScript' => 'forms/_formBlog.phtml'))));
        echo $form;
    }

    public function deleteAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->getParam('id');
        $statusBlogId = new Admin_Model_Blog();
        $vchstado = $statusBlogId->statusBlog($id);
        $rpta = array();
        if (!empty($id)) { 
            try {
                if ($vchstado['vchestado'] == 'I') {
                    $obj = $statusBlogId->updateStatusBlog($id,'A');
                   //$obj = new Application_Entity_RunSql('Blog');
                   //$obj->edit = array('vchestado' => 'A', $obj->getPK() => $id);
                    $rpta['msj'] = 'ok';
                } elseif($vchstado['vchestado'] == 'A') {
                   $obj = $statusBlogId->updateStatusBlog($id,'I');
                    //$obj = new Application_Entity_RunSql('Blog');
                    //$obj->edit = array('vchestado' => 'I', $obj->getPK() => $id);
                    $rpta['msj'] = 'ok';
                }
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

    function action()
    {
       $action="<a data-id=\"__ID__\" class=\"tblaction ico-delete\" title=\"Eliminar\"  href=\"/blog/delete\">Eliminar</a>";
       return $action;
    }

}

