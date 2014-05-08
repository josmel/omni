<?php

class Landing_UbigeoController extends Core_Controller_ActionLanding {

    public function init() {
        parent::init();
        /* Initialize action controller here */
    }

    public function ajaxGetChildrenAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $idUbigeo = $this->_getParam('idubigeo', '');
        $mUbigeo = new Businessman_Model_Ubigeo();
        $listUbigeo = $mUbigeo->findAllByCountryPairs($this->_businessman['codpais'], $idUbigeo);
        
        $return = array();
        foreach ($listUbigeo as $id => $name) {
            $item = array();
            $item['id'] = $id;
            $item['value'] = $name;
            $return[] = $item;
        }
        
        $state = 1;
        $msg = '';  
        
        $return = array(
                'state' => $state, 
                'msg' => $msg, 
                'data' => $return
            );
        
        $this->getResponse()            
            ->setHttpResponseCode(200)
            ->setHeader('Content-type', 'application/json; charset=UTF-8', true)
            ->appendBody(json_encode($return));
    }


}

