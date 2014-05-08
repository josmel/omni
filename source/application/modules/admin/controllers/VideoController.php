<?php
class Admin_VideoController extends Core_Controller_ActionAdmin {
    
    public function init() {
        parent::init();
    }

    public function indexAction() {

        $mVideoType = new Admin_Model_VideoType();
        $videoTypes = $mVideoType->getPairsAll();
        $bTypeCodes = array_keys($videoTypes);
      
        $selectedType = $this->getParam('type', '');
        if(empty($selectedType)) {
            $selectedType = $bTypeCodes[0];
        }
        
        $selectVideos = new Zend_Form_Element_Select('codtvideo');
        $selectVideos->setAttrib('class', 'select-large');
        $selectVideos->setMultiOptions($videoTypes);
        $selectVideos->setValue($selectedType);
        
        $this->view->types = $videoTypes;
        $this->view->selectedType = $selectedType;
        $this->view->selectVideos = $selectVideos;
    }
    
    public function listAction() {
    	$this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true); 
        $params=$this->_getAllParams();
        $iDisplayStart=isset($params['iDisplayStart'])?$params['iDisplayStart']:null;
        $iDisplayLength=isset($params['iDisplayLength'])?$params['iDisplayLength']:null;
        $sEcho=isset($params['sEcho'])?$params['sEcho']:1;     
        $sSearch=isset($params['sSearch'])?$params['sSearch']:'';  
        $obj=new Application_Entity_DataTable('Video',0,$sEcho, false);
        $obj->setIconAction($this->action());
        $query="";
        $query.=!empty($sSearch)? " AND titulo like '%".$sSearch."%' ":" ";
        $obj->setSearch($query);
        
        $this->getResponse()            
	     	->setHttpResponseCode(200)
            ->setHeader('Content-type', 'application/json;charset=UTF-8', true)
            ->appendBody(json_encode($obj->getQuery($iDisplayStart,$iDisplayLength)));   
    }

    public function newAction() {
        $this->_helper->viewRenderer->setNoRender(true);         
        $form = new Admin_Form_Video();
        $obj = new Application_Entity_RunSql('Video');      
        if ($this->_request->isPost()) {
            $dataForm = $this->_request->getPost();
            try{
                $msj=array();                
                if(empty($dataForm['idvideo'])){
                    $dataForm['tmsfeccrea'] = date('Y-m-d H:i:s');
                    $dataForm['vchusucrea'] = $this->_identity->iduser;
                    $obj->save=$dataForm;
                }else{
                    $dataForm['tmsfecmodif'] = date('Y-m-d H:i:s');
                    $dataForm['vchusumodif'] = $this->_identity->iduser;
                   //var_dump($dataForm); return;
                   $obj->edit=$dataForm;
                }
                $this->_redirect('/video');
            }catch (Exception $e){
                echo $e->getMessage();
            }
        }else{
            $this->view->titulo="Nuevo Video";
            $this->view->submit="Grabar Video";
            $this->view->action="/video/new";
            $form->addDecoratorCustom('forms/_formVideo.phtml');  
            echo $form;
        }
    }
    
    public function editAction() {
        $this->_helper->viewRenderer->setNoRender(true);     
        $id=$this->_getParam('id',0);
        $form=new Admin_Form_Video();
        if(!empty($id)){
            $obj=new Application_Entity_RunSql('Video');
            $obj->getone=$id;
            $dataObj =$obj->getone;
            $form->populate($dataObj);
        }
        $this->view->titulo="Editar Video";
        $this->view->submit="Guardar Video";
        $this->view->action="/video/new";
        $form->setDecorators(array(array('ViewScript',
            array('viewScript'=>'forms/_formVideo.phtml'))));  
        echo $form;
    }
    public function deleteAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true); 
        $id=$this->getParam('id');
        $rpta=array();
        if(!empty($id)){
            try{
                $obj=new Application_Entity_RunSql('Video');
                $obj->edit=array('vchestado'=>'D',$obj->getPK()=>$id); 
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
       $action="<a class=\"tblaction ico-edit\" title=\"Editar\" href=\"/video/edit/id/__ID__\">Editar</a>
                    <a data-id=\"__ID__\" class=\"tblaction ico-delete\" title=\"Eliminar\"  href=\"/video/delete\">Eliminar</a>";
       return $action;
    }
}

