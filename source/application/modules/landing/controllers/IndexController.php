<?php

class Landing_IndexController extends Core_Controller_ActionLanding {

    public function init() {
        parent::init();
    }

    public function indexAction() {
        //Productos
        $mProductType = new Shop_Model_ProductType();
        //$mProduct = new Shop_Model_Product();
   
        $productTypes = $mProductType->getAllTypes(false);
        
        //$selectedType = null;
        
//        if (isset($productTypes[$this->getParam('slug', 'todos')])) 
//            $selectedType = $productTypes[$this->getParam('slug', 'todos')];
//        else 
//            $selectedType = $productTypes['todos'];
        
        //$products = $mProduct->getByType('0', $this->_businessman['codpais']);
       
        //$this->view->products = $products;
        $this->view->productTypes = $productTypes;
        //$this->view->selectedType = $selectedType;
        //$this->addYosonVar('defaultCategory', $selectedType['codtpro']);
        //$this->addYosonVar('dataProduct', Zend_Json_Encoder::encode($products), false);
        $this->addYosonVar('urlCart','/cart/ajax-add-product/');
        
        //Banners
        $mBanner = new Admin_Model_Banner();
        $this->view->banners = $mBanner->findAllByType($this->_config['app']['banner']['home'], true);
        
        
        $mProducts = new Shop_Model_Product();
        $this->view->salientProducts  = $mProducts->getSalients($this->_businessman['codpais']);
        
        $mFile = new Admin_Model_File();
        $this->view->LFFiles = $mFile->findAll($this->_config['app']['file']['libertadFinanciera']);
        $this->view->SVFiles = $mFile->findAll($this->_config['app']['file']['saludVerdadera']);
        $this->view->urlfacebook = $this->_config['network']['businessman']['facebook'];
        $this->view->urltwitter = $this->_config['network']['businessman']['twitter'];
        $this->view->urlyoutube = $this->_config['network']['businessman']['youtube'];
    }
}

