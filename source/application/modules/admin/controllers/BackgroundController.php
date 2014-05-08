<?php
class Admin_BackgroundController extends Core_Controller_ActionAdmin {
    
    public function init() {
        parent::init();
    }
    
    public function indexAction() {
    }
    
    public function listAction() {
    	$this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true); 
        $params=$this->_getAllParams();
        $iDisplayStart=isset($params['iDisplayStart'])?$params['iDisplayStart']:null;
        $iDisplayLength=isset($params['iDisplayLength'])?$params['iDisplayLength']:null;
        $sEcho=isset($params['sEcho'])?$params['sEcho']:1;     
        $sSearch=isset($params['sSearch'])?$params['sSearch']:'';  
        $obj=new Application_Entity_DataTable('Background',0,$sEcho, true);
        $obj->setIconAction($this->action());
        $query="";
        $query.=!empty($sSearch)? " AND titulo LIKE '%".$sSearch."%' ":" ";
        $obj->setSearch($query);
        
        $this->getResponse()            
	     	->setHttpResponseCode(200)
            ->setHeader('Content-type', 'application/json;charset=UTF-8', true)
            ->appendBody(json_encode($obj->getQuery($iDisplayStart,$iDisplayLength)));   
    }

    public function newAction() {
        $this->_helper->viewRenderer->setNoRender(true);         
        $form = new Admin_Form_Background();
        $obj = new Application_Entity_RunSql('Background');      
        if ($this->_request->isPost()) {
            $dataForm = $this->getAllParams();
            //var_dump($dataForm); exit;
            $mBackgroundType = new Admin_Model_BackgroundType();
            $backgroundType = $mBackgroundType->findById($dataForm['codtfondo']);
            try{
                $msj=array();                
                if (!$form->imagen->receive()) {
                    $msj[] = $form->getMessages();
                } else {
                    $mImage = new Admin_Model_Image();
                    $fileName = $form->imagen->getFileName();
                    if(!empty($fileName)) {
                        $fInfo = $form->imagen->getFileInfo();
                        $nombre = explode('.', $fInfo['imagen']['name']);
                        $ext = $nombre[count($nombre) - 1];
                        $code = Core_Utils_Utils::getRamdomChars(15);
                        
                        $nombre = $code.'.'.$ext;
                        $dataForm['nombre'] = $nombre;
                        
                        $destinyFolder = ROOT_IMG_DINAMIC."/background/".
                                $backgroundType['codproy'];
                        if(!file_exists($destinyFolder))
                            mkdir($destinyFolder, 0777, true);
                        
                        $newName = $destinyFolder.'/'.$nombre;
                        rename($fileName, $newName);

                        $image = array(
                            'nombre' => $dataForm['nombre'],
                            'vchestado' => 1,
                            'vchusucrea' => $this->_identity->iduser
                        );
                        $dataForm['idimagen'] = $mImage->insert($image);
                    }
                }
                //var_dump($dataForm); exit;
                if(empty($dataForm['idfondo'])){
                    $dataForm['tmsfeccrea'] = date('Y-m-d H:i:s');
                    $dataForm['vchusucrea'] = $this->_identity->iduser;
                    $obj->save=$dataForm;
                }else{
                    $dataForm['tmsfecmodif'] = date('Y-m-d H:i:s');
                    $dataForm['vchusumodif'] = $this->_identity->iduser;
                   //var_dump($dataForm); return;
                   $obj->edit=$dataForm;
                }
                $this->_redirect('/background');
            }catch (Exception $e){
                echo $e->getMessage();
            }
        }else{
            $this->view->titulo="Nuevo Fondo de Aplicación";
            $this->view->submit="Grabar Fondo de Aplicación";
            $this->view->action="/background/new";
            $form->addDecoratorCustom('forms/_formBackground.phtml');  
            echo $form;
        }
    }
    
    public function editAction() {
        $this->_helper->viewRenderer->setNoRender(true);     
        $id=$this->_getParam('id',0);
        $form=new Admin_Form_Background();
        $urlImage='';
        if(!empty($id)){
            $obj=new Application_Entity_RunSql('Background');
            $obj->getone=$id;
            $dataObj =$obj->getone;       
            $idImg=$dataObj['idimagen'];            
            if(!empty($idImg)){
                $objImg=new Application_Entity_RunSql('Image');
                $objImg->getone=$idImg;
                $dataImg=$objImg->getone;
                $urlImage=DINAMIC_URL.'background/LANDI/'.$dataImg['nombre'];                                
            }
            
            $form->populate($dataObj);
        }
        $this->view->urlImage=$urlImage;
        $this->view->titulo="Editar Fondo de Aplicación";
        $this->view->submit="Guardar Fondo de Aplicación";
        $this->view->action="/background/new";
        $form->setDecorators(array(array('ViewScript',
            array('viewScript'=>'forms/_formBackground.phtml'))));  
        echo $form;
    }
    public function deleteAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true); 
        $id=$this->getParam('id');
        $rpta=array();
        if(!empty($id)){
            try{
                $obj=new Application_Entity_RunSql('Background');
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
       $action="<a class=\"tblaction ico-edit\" title=\"Editar\" href=\"/background/edit/id/__ID__\">Editar</a>
                    <a data-id=\"__ID__\" class=\"tblaction ico-delete\" title=\"Eliminar\"  href=\"/background/delete\">Eliminar</a>";
       return $action;
    }
}

