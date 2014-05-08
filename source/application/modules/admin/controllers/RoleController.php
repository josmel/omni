<?php

class Admin_RoleController extends Core_Controller_ActionAdmin
{
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
        $obj=new Application_Entity_DataTable('Role',10,$sEcho, false);
        $obj->setIconAction($this->action());
        $query=!empty($sSearch)? " AND desrol like '%".$sSearch."%'":" ";
        $obj->setSearch($query);
        
        $this->getResponse()            
	     	->setHttpResponseCode(200)
            ->setHeader('Content-type', 'application/json;charset=UTF-8', true)
            ->appendBody(json_encode($obj->getQuery($iDisplayStart,$iDisplayLength)));   
    }

    public function newAction() {
        $this->_helper->viewRenderer->setNoRender(true);         
        $form=new Application_Form_Role();
        $obj=new Application_Entity_RunSql('Role');      
        if ($this->_request->isPost()){   
            $dataForm=$this->_request->getPost();
            
            try{
                $msj=array();                
                
                if(empty($dataForm['idrol'])){
                     $obj->save=$dataForm;
                }else{
                   //var_dump($dataForm); return;
                   $obj->edit=$dataForm;
                }
                $this->_redirect('/role');
            }catch (Exception $e){
                echo $e->getMessage();
            }
        }else{
            $this->view->titulo="Nuevo Rol";
            $this->view->submit="Grabar Rol";
            $this->view->action="/role/new";
            $form->setDecorators(
                    array(
                        array('ViewScript', array('viewScript'=>'forms/_formRole.phtml'))
                        )
                    );  
            echo $form;
        }
    }
    
    public function editAction() {
        $this->_helper->viewRenderer->setNoRender(true);     
        $id=$this->_getParam('id',0);
        $form=new Application_Form_Role();
        if(!empty($id)){
            $obj=new Application_Entity_RunSql('Role');
            $obj->getone=$id;
            $dataObj =$obj->getone;
            $form->populate($dataObj);
        }
        $this->view->titulo="Editar Rol";
        $this->view->submit="Guardar cambios";
        $this->view->action="/role/new";
        $form->setDecorators(array(array('ViewScript',
            array('viewScript'=>'forms/_formRole.phtml'))));  
        echo $form;
    }
    public function deleteAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true); 
        $id=$this->getParam('id');
        $rpta=array();
        if(!empty($id)){
            try{
                $obj=new Application_Entity_RunSql('Role');
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
       $action="<a class=\"tblaction ico-edit\" title=\"Editar\" href=\"/role/edit/id/__ID__\">Editar</a>
                    <a data-id=\"__ID__\" class=\"tblaction ico-delete\" title=\"Eliminar\"  href=\"/role/delete\">Eliminar</a>";
       return $action;
    }
}

