<?php

class Office_TestController extends Core_Controller_Action {

    public function init() {
        parent::init();
        /* Initialize action controller here */
    }
    
    public function generateSlugAction() {
        $this->redirect('/');
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $idProduct = $this->_getParam('product');
        try {
            $mProduct = new Shop_Model_Product();
            $mProduct->updateSlug($idProduct);
            
            echo 'OK';
            $this->redirect('/');
        } catch (Exception $ex) { 
            echo 'ERROR';
        }
    }
    

    public function generateSlugsAction() {
//        $this->redirect('/');
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        try {
            $mProduct = new Shop_Model_Product();
            $mProduct->updateSlugs();
            $mProductType = new Shop_Model_ProductType();
            $mProductType->updateSlugs();
            echo 'OK';
            $this->redirect('/');
        } catch (Exception $ex) { 
            echo 'ERROR';
        }
    }
    
    public function viewLogAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $logType = $this->getParam('type', '');
        
        switch ($logType) {
            case 'FUXIONCARD' : $name = 'fuxioncard.log'; break;
            case 'BILLSERVICE' : $name = 'billservice.log'; break;
            case 'ORDERSERVICE' : $name = 'orderservice.log'; break;
            case 'ORDERSERVICELANDING' : $name = 'orderservicelanding.log'; break;
            case 'ALIGNET' : $name = 'alignetservice.log'; break;
            case 'NEXTPAY' : $name = 'nextpayservice.log'; break;
            case 'MAILERROR' : $name = 'mailerror.log'; break;
            case 'TPP' : $name = 'tppservice.log'; break;
        }
        
        $stream = @fopen(LOG_PATH.'/'.$name, 'a', false);
        if (!$stream) {
            echo "Error al abrir.";
        } else {
            $logFile = file_get_contents(LOG_PATH.'/'.$name);
            echo '<pre>';
            echo $logFile;
            echo '</pre>';
        }
    }

    public function deleteLogAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $logType = $this->getParam('type', '');
        
        switch ($logType) {
            case 'FUXIONCARD' : $name = 'fuxioncard.log'; break;
            case 'BILLSERVICE' : $name = 'billservice.log'; break;
        }
        
        $stream = @fopen(LOG_PATH.'/'.$name, 'a', false);
        if (!$stream) {
            echo "No existe el archivo";
        } else {
            unlink(LOG_PATH.'/'.$name);
        }
    }
    
    public function infoAction(){
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        phpinfo();exit;
    }
}

