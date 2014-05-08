<?php

class Landing_TestController extends Core_Controller_ActionLanding {

    public function init() {
        parent::init();
        /* Initialize action controller here */
    }

    public function generateSlugsAction() {
        $this->redirect('/');
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        try {
            $mProduct = new Shop_Model_Product();
            $mProduct->updateSlugs();
            $mProductType = new Shop_Model_ProductType();
            $mProductType->updateSlugs();
            echo 'OK';
        } catch (Exception $ex) { 
            echo 'ERROR';
        }
    }


}

