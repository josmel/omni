<?php
/**
 *
 * @author Marrselo
 */
class Core_View_Helper_GetMessegerAdmin extends Zend_View_Helper_Abstract {

    /**
     * @param  String
     * @return string
     */
    public function getMessegerAdmin() {

        $message = new Core_Controller_Action_Helper_FlashMessengerCustom();        
        $array = $message->getMessages();
        $arrayClass = array(
            'info' => 'alert alert-info',
            'success' => 'alert alert-success',
            'block' => 'alert alert-block',
            'error' => 'alert alert-error');
        if (count($array) > 0) {
            foreach ($array as $index) {
                echo'<div class="' . $arrayClass[$index->level] . '">
                     <button data-dismiss="alert" class="close" type="button">×</button>
                    ';          
                echo $index->message ;
                echo'</div>';
            }            
        }
    }

}
