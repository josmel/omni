<?php
class Admin_BackgroundColorController extends Core_Controller_ActionAdmin {
    
    public function init() {
        parent::init();
    }
   
    public function indexAction() {
        $backgroundColorCode = $this->_config['app']['backgroundColor']['fondoLanding'];
        $mBackgroundType = new Admin_Model_BackgroundType();
        
        $dataColor = $mBackgroundType->findBCById($backgroundColorCode);
        $color = $dataColor['nombre'];
        
        if($this->getRequest()->isPost()) {
            $newColor = $this->_getParam('color', '');
            if (!empty($newColor)) 
                $mBackgroundType->updateName($backgroundColorCode, $newColor);
            $color = $newColor;
        }
        
        $this->view->hexColor = $color;
    }
}

