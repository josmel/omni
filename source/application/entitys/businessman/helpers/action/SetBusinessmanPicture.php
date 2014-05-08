<?php

class Admin_Action_Helper_SetBusinessmanPicture extends Zend_Controller_Action_Helper_Abstract
{
    public function setBusinessman($nombre,$one,$two,$picture=null)
    {
                if(isset($one)) { 
                    $resize = new Core_Utils_ResizeImage(
                            ROOT_IMG_DINAMIC.'/businessman/origin/'.$nombre
                        );
                    $resize->resizeImage(
                            $one['heigth'],$one['width'], 
                            'exact'
                        );
                 $destinyFolder = ROOT_IMG_DINAMIC.'/businessman/'.$one['heigth'].'x'.$one['width'];
                    if(!file_exists($destinyFolder))
                        mkdir($destinyFolder, 0777, true);
                    $resize->saveImage($destinyFolder.'/'.$nombre);
//                 if($picture!==null){unlink($destinyFolder.'/'.$picture);
//                 }
                 }
                 if(isset($two)) { 
                    $resize = new Core_Utils_ResizeImage(
                            ROOT_IMG_DINAMIC.'/businessman/origin/'.$nombre
                        );
                    $resize->resizeImage(
                            $two['heigth'],$two['width'], 
                            'exact'
                        );
                    $destinyFolder = ROOT_IMG_DINAMIC.'/businessman/'.$two['heigth'].'x'.$two['width'];
                    if(!file_exists($destinyFolder))
                        mkdir($destinyFolder, 0777, true);
                    $resize->saveImage($destinyFolder.'/'.$nombre);
//                     if($picture!==null){unlink($destinyFolder.'/'.$picture);
//                     }
                 }
                
    }
}