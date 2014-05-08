<?php

class Admin_UserController extends Core_Controller_ActionAdmin {

    public function init() {
        parent::init();
    }

    public function indexAction() {
        // action body
    }

    public function listAction() {
    	$this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true); 
        $params=$this->_getAllParams();
        $iDisplayStart=isset($params['iDisplayStart'])?$params['iDisplayStart']:null;
        $iDisplayLength=isset($params['iDisplayLength'])?$params['iDisplayLength']:null;
        $sEcho=isset($params['sEcho'])?$params['sEcho']:1;     
        $sSearch=isset($params['sSearch'])?$params['sSearch']:'';  
        $obj=new Application_Entity_DataTable('User',10,$sEcho, false);
        $obj->setIconAction($this->action());
        $query=!empty($sSearch)? " AND name like '%".$sSearch."%'":" ";
        $obj->setSearch($query);
        
        $this->getResponse()            
	     	->setHttpResponseCode(200)
            ->setHeader('Content-type', 'application/json;charset=UTF-8', true)
            ->appendBody(json_encode($obj->getQuery($iDisplayStart,$iDisplayLength)));   
    }

    public function newAction() {
        $this->_helper->viewRenderer->setNoRender(true);         
        $form=new Application_Form_User();
        $obj=new Application_Entity_RunSql('User');      
        if ($this->_request->isPost()){   
            
            $dataForm=$this->_request->getPost();
            
            $dataForm['password'] = md5($dataForm['password']);
            
            try{
                $msj=array();                
                
                if(empty($dataForm['iduser'])){
                    $dataForm['lastpasschange'] = date('Y-m-d H:i:s');
                    $obj->save=$dataForm;
                }else{
                   //var_dump($dataForm); return;
                   $obj->edit=$dataForm;
                }
                $this->_redirect('/user');
            }catch (Exception $e){
                echo $e->getMessage();
            }
        }else{
            $this->view->titulo="Nuevo Usuario";
            $this->view->submit="Grabar Usuario";
            $this->view->action="/user/new";
            $form->setDecorators(
                    array(
                        array('ViewScript', array('viewScript'=>'forms/_formUser.phtml'))
                        )
                    );  
            echo $form;
        }
    }
    
    public function editAction() {
        $this->_helper->viewRenderer->setNoRender(true);     
        $id=$this->_getParam('id',0);
        $form=new Application_Form_User();
        if(!empty($id)){
            $obj=new Application_Entity_RunSql('User');
            $obj->getone=$id;
            $dataObj =$obj->getone;
            $form->populate($dataObj);
        }
        $this->view->titulo="Editar Usuario";
        $this->view->submit="Guardar cambios";
        $this->view->action="/user/new";
        $form->setDecorators(array(array('ViewScript',
            array('viewScript'=>'forms/_formUser.phtml'))));  
        echo $form;
    }
    public function deleteAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true); 
        $id=$this->getParam('id');
        $rpta=array();
        if(!empty($id)){
            try{
                $obj=new Application_Entity_RunSql('User');
                $obj->edit=array('state'=>0,$obj->getPK()=>$id); 
                $rpta['msj']='ok';
            }  catch (Exception $e){
                $rpta['msj']=$e->getMessage();
            }
        }else{
            $rpta['msj']='faltan datos';
        }
         $this->getResponse()            
            ->setHttpResponseCode(200)
            ->setHeader('Content-type', 'application/json; charset=UTF-8', true)
            ->appendBody(json_encode($rpta));
    }
    
    function action()
    {
       $action="<a class=\"tblaction ico-edit\" title=\"Editar\" href=\"/user/edit/id/__ID__\">Editar</a>
                    <a data-id=\"__ID__\" class=\"tblaction ico-delete\" title=\"Eliminar\"  href=\"/user/delete\">Eliminar</a>";
       return $action;
    }
}

