<?php

class Landing_ProductController extends Core_Controller_ActionLanding {

    public function init() {
        parent::init();
        $this->view->noNav = true;
    }

    public function indexAction() {
        $mProductType = new Shop_Model_ProductType();
        $mProduct = new Shop_Model_Product();
   
        $productTypes = $mProductType->getAllTypes();
        
        $selectedType = null;
        
        if (isset($productTypes[$this->getParam('slug', 'todos')])) 
            $selectedType = $productTypes[$this->getParam('slug', 'todos')];
        else 
            $selectedType = $productTypes['todos'];
        
        $products = $mProduct->getByType('0', $this->_businessman['codpais']);
        
        $result = array();
        for($i = 0; $i < count($products); $i++) {
            $item = array();
            $item['title'] = $products[$i]['desprod'];
            $item['link'] = 
                'http://'.$_SERVER['HTTP_HOST'].$products[$i]['link'];
            $item['img'] = $products[$i]['picture'];
            $item['category'] = $products[$i]['codtpro'];
            $result[] = $item;
        }
        
        $this->view->products = $products;
        $this->view->productTypes = $productTypes;
        $this->view->selectedType = $selectedType;
        $this->addYosonVar('defaultCategory', $selectedType['codtpro']);
        $this->addYosonVar('dataProduct', Zend_Json_Encoder::encode($result), false);
    }

    public function lineAction() {
        $mProductType = new Shop_Model_ProductType();
        $mProduct = new Shop_Model_Product();
   
        $productTypes = $mProductType->getAllTypes(false);
        $selectedType = null;
        /*$slug = $this->getParam('slug', 'todos');
        if (!empty($slug)) 
            $selectedType = $productTypes[$this->getParam('slug', 'todos')];
        else 
            $selectedType = $productTypes['todos'];*/
        $productLines = array();
        $selProducts = array();
        foreach($productTypes as $type) {
            $products = $mProduct->getByLine($type['codtpro'], $this->_businessman['codpais']);
            if($type['codtpro'] == $selectedType['codtpro']) $selProducts = $products;
            $productLines[$type['codtpro']] = $products;
        }
        $this->view->products = $selProducts;
        $this->view->productTypes = $productTypes;
        $this->view->selectedType = $selectedType;
        //$this->addYosonVar('selectCategory', $selectedType['codtpro']);
        $this->addYosonVar('dataProduct', Zend_Json_Encoder::encode($productLines), false);
        $this->view->noNav = true;
    }
    
    public function catalogAction() {
        $mProductType = new Shop_Model_ProductType();
        $mProduct = new Shop_Model_Product();
        
        $productTypes = $mProductType->getAllNoTypes(true, false);
        
        $selectedType = null;
        $slug = $this->getParam('slug', 'todos');
        //var_dump($slug);
        if (!empty($slug)) 
            $selectedType = $productTypes[$this->getParam('slug', 'todos')];
        else 
            $selectedType = $productTypes['todos'];
        
        $productLines = array();
        $selProducts = array();
        foreach($productTypes as $type) {
            //$products = $mProduct->getByNoLine($type['codtpro'], $this->_businessman['codpais']);
            $products = $mProduct->getByType($type['codtpro'], $this->_businessman['codpais'], '', true);
            if($type['codtpro'] == $selectedType['codtpro']) $selProducts = $products;
            $productLines[$type['codtpro']] = $products;
        }
        
        //var_dump($productTypes); exit;
        //var_dump($selProducts);
        $this->view->products = $selProducts;
        $this->view->productTypes = $productTypes;
        $this->view->selectedType = $selectedType;
        $this->addYosonVar('selectCategory', $selectedType['codtpro']);
        $this->addYosonVar('dataProduct', Zend_Json_Encoder::encode($productLines), false);
    }
    
    public function ajaxGetProductsAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true); 
        
        $modelProduct = new Shop_Model_Product();
        
        $products = $mProduct->getByType(
                $this->getParam('category', '0'),
                $this->_businessman['codpais'], 
                'http://'.$_SERVER['HTTP_HOST'],
                $this->getParam('search', '')
            );
        
        
        //$total = 10;
        $return = array('state' => 'OK', 'msg' => '', 
                        'data' => $products
            );
 
        $this->getResponse()            
            ->setHttpResponseCode(200)
            ->setHeader('Content-type', 'application/json; charset=UTF-8', true)
            ->appendBody(json_encode($return));
    }
    
    public function detailAction() {
        $modelProductType = new Shop_Model_ProductType();
        $modelProduct = new Shop_Model_Product();
       
        $urlReturn = '/#product';
        $titleReturn= 'LÍNEAS';
        $type = $this->getParam('type', 'home');
        switch ($type) {
            case 'catalog':
                $titleReturn= 'CATEGORÍAS';
                $urlReturn = '/product/catalog';
                $productTypes = $modelProductType->getAllNoTypes(true, false); 
                break;
            case 'line':
                $urlReturn = '/product/line';
                $productTypes = $modelProductType->getAllTypes(false);
                break;
            default: 
                $productTypes = $modelProductType->getAllTypes(false);
        }
        
        $product = $modelProduct->getBySlug(
                trim($this->getParam('slug', '')), 
                $this->_businessman['codpais']
            );
        
        if(!empty($product)) {
            $this->addYosonVar('idProduct',$product['codprod']);
            $this->addYosonVar('urlCart','/cart/ajax-add-product/');
            $product['link'] = $this->view->url(
                    array('slug' => $product['slug']),
                    'productDetail'
                );
            $product['picture'] = $this->_config['app']['imagesProduct'].'origin-detail/'
                    .$product['codprod'].'.jpg';
        } else {
            $msg = "La ruta no esta relacionada a ningún producto.";
            $this->view->msg = $msg;
        }
        $this->addYosonVar('urlReturn',$urlReturn);
        $this->view->urlReturn = $urlReturn;
        $this->view->titleReturn = $titleReturn;
        $this->view->product = $product;
        $this->view->types = $productTypes;
        //var_dump($product); exit;
    }
}

