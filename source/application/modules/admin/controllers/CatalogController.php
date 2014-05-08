<?php

class Admin_CatalogController extends Core_Controller_ActionAdmin
{
    public function init() {
        parent::init();
    }

    public function indexAction() {
        // action body
        $mProductType = new Shop_Model_ProductType();
        $catalogs = $mProductType->getAllNoTypes(false);
        $this->view->catalogs = $catalogs;
    }

    public function productsAction() {
        // action body
        $slug = $this->getParam('slug', '');
        
        $mProductType = new Shop_Model_ProductType();
        $catalogs = $mProductType->getAllNoTypes(false);
        $selectedCatalog = $catalogs[$slug];
        
        $mProduct = new Shop_Model_Product();
        $products = $mProduct->getByNoLine($selectedCatalog['codtpro'], null, '', false);
        
        $this->view->products = $products;
        $this->view->selectedCatalog = $selectedCatalog;
    }
    
    public function removeProductAction() {
        // action body
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true); 
        
        $slug = $this->getParam('slug', '');
        $idProduct = $this->getParam('id', 0);
        
        $mProductType = new Shop_Model_ProductType();
        $catalogs = $mProductType->getAllNoTypes(false);
        $selectedCatalog = $catalogs[$slug];
        
        //exit;
        $mProductType->removeProduct($selectedCatalog['codtpro'], $idProduct);
        $this->_redirect('/catalog/products/'.$selectedCatalog['slug']);
    }
    
    public function addProductAction() {
        // action body
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $mProductType = new Shop_Model_ProductType();

        
        $idCategory = isset($_SESSION['s_cod_catalog']) ? $_SESSION['s_cod_catalog'] : -1;
        $idProduct = $this->getParam('id');
        
        $rpta=array();
        if(!empty($idProduct) && $idCategory != -1){
            try{
                $mProductType->addProduct($idCategory, $idProduct);
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
    
    public function selectProductAction() {
        $slug = $this->getParam('slug', '');
        
        $mProductType = new Shop_Model_ProductType();
        $catalogs = $mProductType->getAllNoTypes(false);
        $selectedCatalog = $catalogs[$slug];
        $this->view->selectedCatalog = $selectedCatalog;
        
        $_SESSION['s_cod_catalog'] = $selectedCatalog['codtpro'];
    }
    
    public function listProductsAction() {
    	$this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true); 
        $params=$this->_getAllParams();
        $iDisplayStart=isset($params['iDisplayStart'])?$params['iDisplayStart']:null;
        $iDisplayLength=isset($params['iDisplayLength'])?$params['iDisplayLength']:null;
        $sEcho=isset($params['sEcho'])?$params['sEcho']:1;     
        $sSearch=isset($params['sSearch'])?$params['sSearch']:'';  
        $obj=new Application_Entity_DataTable('Product',10,$sEcho, false);
        $obj->setIconAction($this->action());
        
        $mProduct = new Shop_Model_Product();
        $idCategory = isset($_SESSION['s_cod_catalog']) ? $_SESSION['s_cod_catalog'] : '-1';
        
        $idsProducts = $mProduct->getIdsByCategory($idCategory);
        $query = " AND vchestado LIKE 'A' AND NOT codprod IN (".$idsProducts.") ";
        $query.=!empty($sSearch)? " AND desprod like '%".$sSearch."%'"
                                : " ";
        $obj->setSearch($query);
        
        $this->getResponse()            
	     	->setHttpResponseCode(200)
            ->setHeader('Content-type', 'application/json;charset=UTF-8', true)
            ->appendBody(json_encode($obj->getQuery($iDisplayStart,$iDisplayLength)));    
    }
    
    function action() {
       $action="<a data-id=\"__ID__\" class=\"tblaction ico-delete ico-prom\" title=\"Agregar\"  href=\"/catalog/add-product\">Agregar\</a>";
       return $action;
    }
}

