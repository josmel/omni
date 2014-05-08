<?php

class Admin_ProductController extends Core_Controller_ActionAdmin
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
        $iDisplayLength=isset($params['iDisplayLength'])?$params['iDisplayLength']:0;
        $sEcho=isset($params['sEcho'])?$params['sEcho']:1;     
        $sSearch=isset($params['sSearch'])?$params['sSearch']:'';  
        $obj=new Application_Entity_DataTable('Product',$iDisplayLength,$sEcho, false);
        $obj->setIconAction($this->action());
        $query=!empty($sSearch)? " AND desprod like '%".$sSearch."%' OR shorttext like '%"
                                 .$sSearch."%' OR text like '%".$sSearch."%'"
                                : " ";
        $obj->setSearch($query);
        
        $this->getResponse()            
	     	->setHttpResponseCode(200)
            ->setHeader('Content-type', 'application/json;charset=UTF-8', true)
            ->appendBody(json_encode($obj->getQuery($iDisplayStart,$iDisplayLength)));   
    }
    
    public function editAction() {
        $this->_helper->viewRenderer->setNoRender(true);     
        $id = $this->_getParam('id',0);
        $form = new Shop_Form_Product();
        $obj=new Application_Entity_RunSql('Product');
        
        if ($this->_request->isPost()) {   
            $dataForm=$this->_request->getPost();
            
            try {
                $msj=array();                
                
                if (!$form->catalog_image->receive()) {
                    $msj[] = $form->getMessages();
                } else {
                    $fileName = $form->catalog_image->getFileName();
                    if(!empty($fileName)) {
                        $fInfo = $form->catalog_image->getFileInfo();
                        $nombre = explode('.', $fInfo['catalog_image']['name']);
                        $ext = $nombre[count($nombre) - 1];
                        $code = $dataForm['codprod'];
                        
                        $dataForm['imgextcat'] = $ext;
                        $nombre = $code.'.'.$ext;
                        
                        //ORIGIN
                        $destinyFolder = ROOT_IMG_DINAMIC."/product/origin-catalog";
                        if(!file_exists($destinyFolder))
                            mkdir($destinyFolder, 0777, true);
                        $newName = $destinyFolder.'/'.$nombre;
                        rename($fileName, $newName);
                        
                        //CATALOG
                        $resize = new Core_Utils_ResizeImage($newName);
                        $destinyFolder = ROOT_IMG_DINAMIC."/product/catalog";
                        if(!file_exists($destinyFolder))
                            mkdir($destinyFolder, 0777, true);
                        $resize->resizeImage(138, 122, 'exact');
                        $resize->saveImage($destinyFolder.'/'.$nombre);
                        
                        //MINI
                        $resize = new Core_Utils_ResizeImage($newName);
                        $destinyFolder = ROOT_IMG_DINAMIC."/product/mini";
                        if(!file_exists($destinyFolder))
                            mkdir($destinyFolder, 0777, true);
                        $resize->resizeImage(38, 34, 'exact');
                        $resize->saveImage($destinyFolder.'/'.$nombre);
                    }
                }
                
                if (!$form->detail_image->receive()) {
                    $msj[] = $form->getMessages();
                } else {
                    $fileName = $form->detail_image->getFileName();
                    if(!empty($fileName)) {
                        $fInfo = $form->detail_image->getFileInfo();
                        $nombre = explode('.', $fInfo['detail_image']['name']);
                        $ext = $nombre[count($nombre) - 1];
                        $code = $dataForm['codprod'];
                        
                        $dataForm['imgextdet'] = $ext;
                        $nombre = $code.'.'.$ext;
                        
                        //ORIGIN
                        $destinyFolder = ROOT_IMG_DINAMIC."/product/origin-detail";
                        if(!file_exists($destinyFolder))
                            mkdir($destinyFolder, 0777, true);
                        $newName = $destinyFolder.'/'.$nombre;
                        rename($fileName, $newName);
                        
                        //DETAIL LANDING
                        $resize = new Core_Utils_ResizeImage($newName);
                        $destinyFolder = ROOT_IMG_DINAMIC."/product/detail-landing";
                        if(!file_exists($destinyFolder))
                            mkdir($destinyFolder, 0777, true);
                        $resize->resizeImage(182, 250, 'exact');
                        $resize->saveImage($destinyFolder.'/'.$nombre);
                    }
                }
                
                if(empty($dataForm['codprod'])){
                     $obj->save=$dataForm;
                }else{
                   //var_dump($dataForm); return;
                   $obj->edit=$dataForm;
                }
                
                $cache = Zend_Registry::get('Cache');
                $cache->clean(
                    Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG,
                    array('product')
                );
                $this->_redirect('/product');
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        } else {
            if (!empty($id)) {
                $obj->getone=$id;
                $dataObj =$obj->getone;
                $form->populate($dataObj);
            }
            
            $mProduct = new Shop_Model_Product();
            $product = $mProduct->getById($id);
            $urlImgDet = DINAMIC_URL.'product/origin-detail/'.$id.'.'.$product['imgextdet']; 
            $urlImgCat = DINAMIC_URL.'product/origin-catalog/'.$id.'.'.$product['imgextcat']; 

            $this->view->urlImgDet = $urlImgDet;
            $this->view->urlImgCat = $urlImgCat;
        
            $this->view->titulo="Editar Producto";
            $this->view->submit="Guardar cambios";
            $this->view->action="/product/edit";
            $form->setDecorators(array(array('ViewScript',
                array('viewScript'=>'forms/_formProduct.phtml'))));  
            echo $form;
        }
    }
    
    function action()
    {
       $action="<a class=\"tblaction ico-edit\" title=\"Editar\" href=\"/product/edit/id/__ID__\">Editar</a>";
       return $action;
    }
}

