<?php

class Challenge_OperationController extends Core_Controller_ActionChallenge
{
//    public function preDispatch() 
//    {
//        $action = $this->getRequest()->getActionName();
//        $auth = new Zend_Session_Namespace('authDesafio');
////        Zend_Debug::dump($auth->auth);exit;
//        if(empty($auth->auth)){
//            if(APPLICATION_ENV == 'local'){
//                $this->_redirect('http://local.office/');
//            }else{
//                $this->_redirect('http://fuxion.onlinestudioproductions.com');
//            }
//        }
//    }
    
    public function indexAction(){
        
    }

    public function uploadImgAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        try {
            $step = new Zend_Session_Namespace('step');
            $upload = new Zend_File_Transfer_Adapter_Http();
            $path = 'desafio/origin/';
            $upload->setDestination(ROOT_IMG_DINAMIC.'/'.$path);
//            $upload->addValidator('Count', false, array('min' =>1, 'max' => 1))
//                   ->addValidator('IsImage', false);
            $files = $upload->getFileInfo();
            if(!isset($step->upload)){
                $step->upload = array(
                    'wincha' => null,
                    'frente' => null,
                    'perfil' => null,
                    'otro' => null,
                    'active' => FALSE,
                    'domain' => DINAMIC_URL.$path,
                ); 
            }
            $msj = 'ok'; $inputExist = FALSE; $imgUpload = array(); $state = 1;
            $inputFile = array('wincha', 'frente', 'perfil', 'otro');
            
            $newFilename = "";
            foreach ($files as $file => $info) {
                if(in_array($file, $inputFile)){
                    //Redimensionar a  150 x 139
                    
                    $newFilename = uniqid(time(), true).'-'.$upload->getFileName($file,false);
                    $upload->addFilter('Rename', $newFilename);
                    
                    if (!$upload->isUploaded($file)) {
                        $state = 0;
                        $msj = "Ocurrio un error, intente de nuevo";
                        continue;
                    }
                    if (!$upload->isValid($file)) {
                        $state = 0;
                        $msj = "Error, suba una imagen";
                        continue;
                    }
                    
                    $imgUpload[$file] = $newFilename;
                    $inputExist = TRUE;
                }else{
                    $inputExist = FALSE;
                    $state = 0;
                    $msj = "InputName incorrect";
                }
            }
//            echo $msj;
            if($inputExist) { 
                $upload->receive();
                
                $origin = $upload->getDestination();
                $destinyFolder = ROOT_IMG_DINAMIC.'/desafio/';
                //echo $origin.'/'.$newFilename; exit;
                //Redimensionamiento
                $resize = new Core_Utils_ResizeImage($origin.'/'.$newFilename);
                $resize->resizeImage(150, 139, 'exact');
                $resize->saveImage($destinyFolder.$newFilename);
            }
            
            $step->upload = array_merge($step->upload, $imgUpload);
            $emptyUpload = 0;
            if(count($step->upload) > 0){
                foreach ($step->upload as $key => $value) {
                    if(empty($value) && $key != 'active' && $key != 'otro'){
                        $emptyUpload ++;
                    }
                }
            }
            if($emptyUpload > 0)
                $step->upload['active'] = FALSE;
            else
                $step->upload['active'] = TRUE;
            
            $step->upload['state'] = $state;
            $step->upload['msj'] = $msj;
            $rpta = $step->upload;
            echo json_encode($rpta);exit();            
        } catch (Exception $exc) {
            $msj = $exc->getMessage();
        }
    }
    
    public function getMentorAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $state = 0;
        $msg = "El código del mentor es incorrecto";
        $idBusinessman = $this->_getParam('idmentor', '-1');
        $dataMentor = array();
        if($idBusinessman != $this->_identity['codemp']) {
            $mBusinessman = new Businessman_Model_Businessman();
            $businessman = $mBusinessman->findDataViewById($idBusinessman);
            if (!empty($businessman)) {
                $dataMentor = array(
                    'nombre' => $businessman['nomempr'],
                    'apepaterno' => $businessman['appempr'],
                    'apematerno' => $businessman['apmempr'],
                    'email' => $businessman['emaempr'],
                    'celular' => $businessman['celular'],
                    'telefono' => $businessman['telefono'],
                );
                $state = 1;
                $msg= "Se encontró al mentor";
            }
        }else{
            $msg= "El mentor no puede ser uno mismo";
        }  
        
        $result = array(
            'state' => $state,
            'msg' => $msg,
            'dataMentor' => $dataMentor
        );
        
        
        $this->getResponse()            
             ->setHttpResponseCode(200)
             ->setHeader('Content-type', 'application/json;charset=UTF-8', true)
             ->appendBody(Zend_Json_Encoder::encode($result));
    }
    
    public function validateMentorAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $state = 'false';
        
        $idBusinessman = $this->_getParam('codmentor', '-1');
        if($idBusinessman != $this->_identity['codemp']) {
            $mBusinessman = new Businessman_Model_Businessman();
            $businessman = $mBusinessman->findDataViewById($idBusinessman);
            if (!empty($businessman)) {
                $state = 'true';
            }
        }
        
        echo $state;
    }
    
    public function ajaxDataCalendarAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $dataCalendar = array();
        
        $tBegin = $this->_getParam('start', 0);
        $tEnd = $this->_getParam('end', 0);
        
        if ($tBegin == 0 || $tEnd == 0) return;
        
        $authDesafio = new Zend_Session_Namespace('authDesafio');
        $idCompetitor = $authDesafio->idParticipante;
        $timeStampWeek = 86400*7;
        
        $begin = new Zend_Date();
        $begin->setTimestamp($tBegin);
        
        $end = new Zend_Date();
        $end->setTimestamp($tEnd);
        
        $nroBegin = $begin->get(Zend_Date::YEAR.''.Zend_Date::MONTH.''.Zend_Date::DAY);
        $nroEnd = $end->get(Zend_Date::YEAR.''.Zend_Date::MONTH.''.Zend_Date::DAY);
        //GET CICLOS y FECHA DE ACTIVACION
        $mCicloDet = new Challenge_Model_DetaCiclo();
        $mCiclos = new Challenge_Model_CicloParticipantes();
        $ciclos = $mCiclos->getByDateRange($idCompetitor, $begin, $end);
        //var_dump($ciclos);
        foreach ($ciclos as $ciclo) {
            $cicloStart = substr($ciclo['pfInicio'], 0, 10);
            $cicloEnd = substr($ciclo['pfFin'], 0, 10);
            $ciclo['ninicio'] = str_replace("-", "", $cicloStart);
            $ciclo['nfin'] = str_replace("-", "", $cicloEnd);
            
            if ($nroBegin <= $ciclo['ninicio'] && $ciclo['ninicio'] <= $nroEnd) {
                $item = array( 
                    'title' => "Inicio del<br> Desafío Fuxion",
                    'className' => "evtDesafio",
                    'start' =>$cicloStart,
                    'textColor' => "#5586f5"
                );
                $dataCalendar[] = $item;
            }
            
            if ($ciclo['nfin'] >= $nroBegin && $ciclo['nfin'] <= $nroEnd) {
                $item = array( 
                    'title' => "Fin del<br> Desafío Fuxion",
                    'className' => "evtDesafio",
                    'start' => $cicloEnd,
                    'textColor' => "#5586f5"
                );
                $dataCalendar[] = $item;
            }
            
            //FECHAS DE ACTIVACIÓN
            //$aWeekDay = $this->_config['app']['activationWeekDay'];
            $cicloStartDate = new Zend_Date($cicloStart, Zend_Date::YEAR.'-'.Zend_Date::MONTH.'-'.Zend_Date::DAY); 
            $aWeekDay = $cicloStartDate->get(Zend_Date::WEEKDAY_DIGIT);
            $cicloDate = new Zend_Date($cicloStart, Zend_Date::YEAR.'-'.Zend_Date::MONTH.'-'.Zend_Date::DAY); 
            
            $cicloDate->addDay(1);
            $cicloWeekDate = $cicloDate->get(Zend_Date::WEEKDAY_DIGIT);
            $cicloEndDate = new Zend_Date($cicloEnd, Zend_Date::YEAR.'-'.Zend_Date::MONTH.'-'.Zend_Date::DAY); 
            
            if ($cicloWeekDate > $aWeekDay) $cicloDate->addDay(7 - ($cicloWeekDate - $aWeekDay));
            else $cicloDate->addDay($aWeekDay - $cicloWeekDate);
            
            $cicloDateTime = $cicloDate->getTimestamp();
            $cicloEndDateTime = $cicloEndDate->getTimestamp();
            
            while ($tEnd >= $cicloDateTime && $cicloEndDateTime >= $cicloDateTime) {
                $item = array( 
                    'title' => "Fecha de<br> Activación",
                    'className' => "evtActive",
                    'start' => $cicloDate->get(Zend_Date::YEAR.'-'.Zend_Date::MONTH.'-'.Zend_Date::DAY),
                    'textColor' => "#ef257b"
                );
                $dataCalendar[] = $item;
                
                $cicloDate->addTimestamp($timeStampWeek);
                $cicloDateTime = $cicloDateTime + $timeStampWeek;
            }
            
            //GET MEDIDAS
            $detalles = $mCicloDet->findAllByIdCicParti($ciclo['idcicparti']);
            
            foreach ($detalles as $detalle) {
                $detFec = substr($detalle['tmsfeccrea'], 0, 10);
                $nDetFec = str_replace("-", "", $detFec);

                //GET MEDIDAS
                if ($nroBegin <= $nDetFec && $nDetFec <= $nroEnd) {
                    $item = array( 
                        'title' => "Medidas",
                        'className' => "evtMeasure",
                        'start' =>$detFec,
                        'id' => $detalle['iddetacic'],
                        'textColor' => "#c35df2"
                    );
                    $dataCalendar[] = $item;
                }
            }
        }
        
        //GET COMPRAS
        $mBuyDocument = new Biller_Model_BuyDocument();
        $buys = $mBuyDocument->findByBusinessman($this->_identity['codemp'], $begin, $end);
        
        foreach ($buys as $buy) {
            $item = array( 
                'title' => $buy['cantidad']." productos<br> comprados",
                'className' => "evtProduct",
                'start' => substr($buy['fecdven'], 0, 10),
                'id' => $buy['iddven'],
                'textColor' => "#ebc721"
            );
            $dataCalendar[] = $item;
        }
        
//         $item = array( 
//                'title' => "3 productos<br> comprados",
//                'className' => "evtProduct",
//                'start' => '2014-01-16',
//                'id' => 4,
//                'textColor' => "#ebc721"
//            );
//            $dataCalendar[] = $item;
            
        //var_dump($dataCalendar); 
        
       // return;
        $this->getResponse()            
             ->setHttpResponseCode(200)
             ->setHeader('Content-type', 'application/json;charset=UTF-8', true)
             ->appendBody(Zend_Json_Encoder::encode($dataCalendar));
    }
    
    public function ajaxDataCycleDetailAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $idCycleDetail = $this->_getParam('id', -1);
        
        if($idCycleDetail == - 1) return;
        
        $mCycleDetail  = new Challenge_Model_DetaCiclo();
        $cycleDetail = $mCycleDetail->findbyId($idCycleDetail);
        
        $data['peso'] = $cycleDetail['peso'];
        $data['cintura'] = $cycleDetail['cintura'];
        $data['cadera'] = $cycleDetail['cadera'];
        $data['pecho'] = $cycleDetail['pecho'];
        $data['espalda'] = $cycleDetail['espalda'];

        $path = 'desafio/';
        
        if(!empty($cycleDetail['fotowincha'] ))
            $data['fotowincha'] = DINAMIC_URL.$path.$cycleDetail['fotowincha'];
        if(!empty($cycleDetail['fotoperfil'] ))
            $data['fotoperfil'] = DINAMIC_URL.$path.$cycleDetail['fotoperfil'];
        if(!empty($cycleDetail['fotofrente'] ))
            $data['fotofrente'] = DINAMIC_URL.$path.$cycleDetail['fotofrente'];
        if(!empty($cycleDetail['fotootros'] ))
            $data['fotootros'] = DINAMIC_URL.$path.$cycleDetail['fotootros'];
        
        //var_dump($data); exit;
        $this->getResponse()            
             ->setHttpResponseCode(200)
             ->setHeader('Content-type', 'application/json;charset=UTF-8', true)
             ->appendBody(Zend_Json_Encoder::encode($data));
   }
   
   public function ajaxDataDetailOrderAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $idBuyDocument = $this->_getParam('id', -1);
        
        if($idBuyDocument == - 1) return;
        
        $mBuyDocument = new Biller_Model_BuyDocument();
        $details = $mBuyDocument->getDetails($idBuyDocument);
        
        $data = array();
        $rpta= array();
        foreach($details as $detail) {
            $item['name'] = $detail['desprod'];
            $item['quantity'] = $detail['candpro'];
            
            if(empty($detail['slug'])) $detail['slug'] = $detail['codprod'];
            //$row['link'] = $vhUrl->url( array('slug' => $row['slug']), 'productDetail');
            $item['img'] = $this->_config['app']['imagesProduct'].$detail['codprod'].'.jpg';
            
            $data[] = $item;
        }
        $rpta["products"]=$data;
        //var_dump($data);
        
        $this->getResponse()            
             ->setHttpResponseCode(200)
             ->setHeader('Content-type', 'application/json;charset=UTF-8', true)
             ->appendBody(Zend_Json_Encoder::encode($rpta));
   }
}