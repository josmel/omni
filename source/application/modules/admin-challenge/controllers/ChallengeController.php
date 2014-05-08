<?php

class AdminChallenge_ChallengeController extends Core_Controller_ActionAdminChallenge {
    
    public function init() {
        parent::init();      
    }
    
    public function indexAction() {
        $mCycle = new Challenge_Model_Ciclo();
        $cycles = $mCycle->getByEstado('A');
        
        $selectedCycle = $this->_getParam('cycle', 0);
        if($selectedCycle == 0) {
            $selectedCycle = $cycles[0];
            $selectedCycles = $cycles[0]['idciclo'];
        } else {
            foreach ($cycles as $cycle) {
                if($selectedCycle == $cycle['idciclo']){
                    $selectedCycles = $cycle['idciclo'];
                    $selectedCycle = $cycle;
                }
            }
        } 
        
        $selectedWeek = $this->_getParam('week', 0);
        $weeks = array();
        for ($i = 1; $i <= $selectedCycle['nrosemana']; $i++) 
            $weeks[] = $i;
        
        if($selectedWeek == 0) $selectedWeek = $weeks[0];
        
        $this->view->weeks = $weeks;
        $this->view->cycles = $cycles;
        $this->view->selectedCycle = $selectedCycles;
        $this->view->selectedWeek = $selectedWeek;
        
        $sessionParams = new Zend_Session_Namespace('params');
        $sessionParams->cicle = isset($selectedCycles)?$selectedCycles:0;
        $sessionParams->week = isset($selectedWeek)?$selectedWeek:0;
    }
    
    public function detailAction() {
        $mCycle = new Challenge_Model_Ciclo();
        $cycles = $mCycle->getByEstado('A');
        
        $selectedCycle = $this->_getParam('cycle', 0);
        if($selectedCycle == 0) {
            $selectedCycle = $cycles[0];
            $selectedCycles = $cycles[0]['idciclo'];
        } else {
            foreach ($cycles as $cycle) {
                if($selectedCycle == $cycle['idciclo']){
                    $selectedCycles = $cycle['idciclo'];
                    $selectedCycle = $cycle;
                }
            }
        } 
        
        $selectedWeek = $this->_getParam('week', 0);
        $weeks = array();
        for ($i = 1; $i <= $selectedCycle['nrosemana']; $i++) 
            $weeks[] = $i;
        
        if($selectedWeek == 0) $selectedWeek = $weeks[0];
        
        $this->view->weeks = $weeks;
        $this->view->cycles = $cycles;
        $this->view->selectedCycle = $selectedCycles;
        $this->view->selectedWeek = $selectedWeek;
        
        $sessionParams = new Zend_Session_Namespace('params');
        $sessionParams->cicle = isset($selectedCycles)?$selectedCycles:0;
        $sessionParams->week = isset($selectedWeek)?$selectedWeek:0;
    }
    
    public function listAction() {
//        $selectedWeek = $this->_getParam('week', 0);
//        $selectedCycle = $this->_getParam('cycle', 0);
        
        $sessionParams = new Zend_Session_Namespace('params');
        $selectedWeek = (int)($sessionParams->week - 1);
        $selectedCycle = $sessionParams->cicle;
                
        if($selectedCycle == 0) { 
            $mCycle = new Challenge_Model_Ciclo();
            $cycles = $mCycle->getByEstado('A');
            $selectedCycle = $cycles[0]['idciclo'];
        }
        
        $tqParticipante = new QueryTable_ParticipantesAvance($selectedCycle, $selectedWeek);
        //$mCiclo = new Challenge_Model_Ciclo();
    	$this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true); 
        $params=$this->_getAllParams();
        $iDisplayStart=isset($params['iDisplayStart'])?$params['iDisplayStart']:null;
        $iDisplayLength=isset($params['iDisplayLength'])?$params['iDisplayLength']:null;
        $sEcho=isset($params['sEcho'])?$params['sEcho']:1;     
        $sSearch=isset($params['sSearch'])?$params['sSearch']:'';  
        $tqParticipante->setSearch($sSearch);
        
        $obj=new Application_Entity_DataTable('',$iDisplayLength,$sEcho, false);
        
        $this->getResponse()            
	    ->setHttpResponseCode(200)
            ->setHeader('Content-type', 'application/json;charset=UTF-8', true)
            ->appendBody(Zend_Json_Encoder::encode($obj->getSoloQuery($tqParticipante, $iDisplayStart, $iDisplayLength)));   
    }
    
    public function listDetailAction() {
//        $selectedWeek = $this->_getParam('week', 0);
//        $selectedCycle = $this->_getParam('cycle', 0);
        
        $sessionParams = new Zend_Session_Namespace('params');
        $selectedWeek = (int)($sessionParams->week - 1);
        $selectedCycle = $sessionParams->cicle;
        
        if($selectedCycle == 0) { 
            $mCycle = new Challenge_Model_Ciclo();
            $cycles = $mCycle->getByEstado('A');
            $selectedCycle = $cycles[0]['idciclo'];
        }
        
        $tqParticipante = new QueryTable_ParticipantesDetalle($selectedCycle, $selectedWeek);
        //$mCiclo = new Challenge_Model_Ciclo();
    	$this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true); 
        $params=$this->_getAllParams();
        $iDisplayStart=isset($params['iDisplayStart'])?$params['iDisplayStart']:null;
        $iDisplayLength=isset($params['iDisplayLength'])?$params['iDisplayLength']:null;
        $sEcho=isset($params['sEcho'])?$params['sEcho']:1;     
        $sSearch=isset($params['sSearch'])?$params['sSearch']:'';  
        $tqParticipante->setSearch($sSearch);
        
        $obj=new Application_Entity_DataTable('',$iDisplayLength,$sEcho, false);
        
        $this->getResponse()            
	     	->setHttpResponseCode(200)
            ->setHeader('Content-type', 'application/json;charset=UTF-8', true)
            ->appendBody(json_encode($obj->getSoloQuery($tqParticipante, $iDisplayStart, $iDisplayLength)));   
    }
    
    public function ajaxSendMessageAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true); 
        
        $params = $this->getAllParams();
        try {
            if(!empty($params['message']) && !empty($params['subject']) && !empty($params['ids'])){
                $messsage = $params['message'];
                $subject = $params['subject'];
                $idCompetitors = explode(',', $params['ids']);

                $competitorMH = $this->getHelper('competitorMail');
                $competitorMH->mailMessage($subject, $messsage, $idCompetitors, $this->getHelper('mail'));       

                $msg = "Se Enviaron los mensajes Correctamente.";
                $state = 1;
            }else{
                $msg = "Parametros insuficientes";
                $state = 0;
            }
        } catch (Exception $ex) {
            $msg = "Error de Conexión.";
            $state = 0;
        }
        
        $return = array(
            'state' => $state, 
            'msg' => $msg
        );
        
        $this->getResponse()            
            ->setHttpResponseCode(200)
            ->setHeader('Content-type', 'application/json; charset=UTF-8', true)
            ->appendBody(json_encode($return));
    }
    
    public function ajaxSendAlertAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true); 
        
        $params = $this->getAllParams();
        try {
            $idCompetitors = !empty($params['ids'])?explode(',', $params['ids']):null;

            $competitorMH = $this->getHelper('competitorMail');
            $competitorMH->mailAlert($this->getHelper('mail'), $idCompetitors);  

            $msg = "Se Enviaron los alertas Correctamente.";
            $state = 1;
        } catch (Exception $ex) {
            $msg = "Error de Conexión.";
            $state = 0;
        }
        
        $return = array(
            'state' => $state, 
            'msg' => $msg
        );
        
        $this->getResponse()            
            ->setHttpResponseCode(200)
            ->setHeader('Content-type', 'application/json; charset=UTF-8', true)
            ->appendBody(json_encode($return));
    }
    
    public function reactiveAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $paramsAll = $this->getAllParams();
        try {
            if(!empty($paramsAll['ids'])){
                $ids = explode(',', $paramsAll['ids']);
                $params = array(
                    'estadoofi' => 1,
                    'vchestado' => 1,
                );
                $tblParticipantes = new Challenge_Model_Participantes();
                $reactivate = $tblParticipantes->reactivate($ids, $params);
                $msg = "Se Activaron los usuarios Correctamente.";
                $state = 1;
            }else{
                $msg = "Parametros insuficientes";
                $state = 0;
            }
        } catch (Exception $exc) {
            $msg = "Error de Conexión.";
            $state = 0;
        }
        $return = array(
            'state' => $state, 
            'msg' => $msg
        );
        
        $this->getResponse()            
            ->setHttpResponseCode(200)
            ->setHeader('Content-type', 'application/json; charset=UTF-8', true)
            ->appendBody(json_encode($return));
    }
    
    public function detailProgressAction()
    {
        $codemp = $this->getParam('codemp', null);
        $idciclo = $this->getParam('idciclo', null);
        
        if(empty($idciclo) && empty($codemp)){
            $this->redirect('/challenge');exit;
        }
        
        $tblCiclo = new Challenge_Model_Ciclo();
        $ciclo = $tblCiclo->findById($idciclo);
        
//        $auth = new Zend_Session_Namespace('authDesafio');
//        $step = new Zend_Session_Namespace('step');
//        $form = new Challenge_Form_StepOneA();
        
        $tblParticipante =  new Challenge_Model_Participantes();
        $tblCicloParticpantes = new Challenge_Model_CicloParticipantes();
        $tblDetaCiclo = new Challenge_Model_DetaCiclo();
        
        $dataParticipante = $tblParticipante->findRowByCodemp($codemp, true);
        $dataCicloParticipante = $tblCicloParticpantes->findRowByIdPartiIdciclo($dataParticipante['idparti'], 
                $ciclo['idciclo'], true);
        $dataIniDetaCiclo = $tblDetaCiclo->getRowMeasure($dataCicloParticipante['idcicparti'], 'ini', true);
        $dataFinDetaCiclo = $tblDetaCiclo->getRowMeasure($dataCicloParticipante['idcicparti'], 'fin', true);
        
        $fechaIni = new DateTime($dataFinDetaCiclo['fecfin']);
        $fechaIni->modify("+1 day");
        $fechaIniDC = $fechaIni->format('Y-m-d H:i:s');
        $fechaFin = new DateTime($fechaIniDC);
        $fechaFin->modify("+6 day");
        $fechaFinDC = $fechaFin->format('Y-m-d H:i:s');
        
        $enableAdd = date("Ymd") >= $fechaIni->format('Ymd') ? true : false;
        
        $numWeekHelper = $this->getHelper('numWeek');
        $numWeek = $numWeekHelper->numWeek(date('Y-m-d', strtotime($dataIniDetaCiclo['fecini'])), 
                date('Y-m-d', strtotime($fechaFinDC)), true);
        
        $stateCompetitorHelper = $this->getHelper('stateCompetitor');
        $stateP = $stateCompetitorHelper->stateCompetitor($dataFinDetaCiclo['fecfin'],$dataParticipante['vchestado']);
        $this->view->estadoParticipante = $stateP;
        
//        if($this->_request->isPost()){
//            $allParams = $this->getAllParams();
//            $dataStepUpload = isset($step->upload)?$step->upload:null;
//            
////            $validateInsert = $tblDetaCiclo->validateInsert(date('Y-m-d H:i:s'), 
////                    $dataCicloParticipante['idcicparti'], true);
//            if ($form->isValidPartial($allParams) && !empty($dataStepUpload) && 
//                    $dataStepUpload['active'] && $auth->empActive && $dataParticipante['vchestado'] == 'A' &&
//                    $dataCicloParticipante['vchestado'] == 'A' && $enableAdd) {
//                // elements present all passed validations
////                exit;
//                $dataDetaCiclo = $allParams;
//                $dataDetaCiclo['idcicparti'] = $dataCicloParticipante['idcicparti'];
//                $dataDetaCiclo['idtipmusc'] = $dataFinDetaCiclo['idtipmusc'];
//                $dataDetaCiclo['fotowincha'] = $step->upload['wincha'];
//                $dataDetaCiclo['fotofrente'] = $step->upload['frente'];
//                $dataDetaCiclo['fotoperfil'] = $step->upload['perfil'];
//                $dataDetaCiclo['fotootros'] = $step->upload['otro'];
//                $dataDetaCiclo['semana'] = $numWeek;
//                $dataDetaCiclo['fecini'] = $fechaIniDC;
//                $dataDetaCiclo['fecfin'] = $fechaFinDC;
//                $dataDetaCiclo['vchestado'] = 1;
//                $dataDetaCiclo['vchusucrea'] = $auth->auth['codemp'];
//                $dataDetaCiclo['tmsfeccrea'] = date('Y-m-d H:i:s');
//                $saveDetaCiclo = $tblDetaCiclo->insert($dataDetaCiclo);
//                Zend_Session::namespaceUnset('step');
//                $this->_redirect('/index/progress');
////                exit;
//            } else {
//                // one or more elements tested failed validations
//            }
//        }
        
        $details = $tblDetaCiclo->findAllByIdCicParti($dataCicloParticipante['idcicparti']);
        
        $arrayX = array();
        $arrayY = array();
        for($i = 1; $i <= $ciclo['nrosemana']; $i++) {
            $arrayX[] = "SEM ".$i;
            $arrayY[] = null;
        }

        $dataChart = array();
//            $lastWeight = -1;
        $i = 1;
        
        $path = 'desafio/';
        
        $imgs = array();
        foreach ($details as $detail) {
            $item = array();
            $itemImgs = array();
            
            if(!empty($detail['fotowincha'])) 
                $itemImgs['fotowincha'] = CHALLENGE_DINAMIC_URL.$path.$detail['fotowincha'];
            if(!empty($detail['fotofrente'])) 
                $itemImgs['fotofrente'] = CHALLENGE_DINAMIC_URL.$path.$detail['fotofrente'];
            if(!empty($detail['fotoperfil'])) 
                $itemImgs['fotoperfil'] = CHALLENGE_DINAMIC_URL.$path.$detail['fotoperfil'];
            if(!empty($detail['fotootros'])) 
                $itemImgs['fotootros'] = CHALLENGE_DINAMIC_URL.$path.$detail['fotootros'];
            
            $imgs[] = $itemImgs;
            
            $item['peso'] = $detail['peso'];
            $item['cintura'] = $detail['cintura'];
            $item['cadera'] = $detail['cadera'];
            $item['pecho'] = $detail['pecho'];
            $item['espalda'] = $detail['espalda'];
//                if($lastWeight == -1) {
//                    $cambioPeso = 0;
//                } else{
//                    $cambioPeso = $lastWeight - $detail['peso'];
//                }
//                $lastWeight = $detail['peso'];
//                $item['cambioPeso'] = $cambioPeso;
            $arrayY[$i-1] = (int)$detail['peso'];
            $dataChart['SEM '.$i] = $item;
            $i++;
        }

//            var_dump($arrayX);
//            var_dump($arrayY);
//            var_dump($dataChart); 

        $this->addYosonVar('chartX', Zend_Json_Encoder::encode($arrayX), false);
        $this->addYosonVar('chartY', Zend_Json_Encoder::encode($arrayY), false);
        $this->addYosonVar('chartData', Zend_Json_Encoder::encode($dataChart), false);
    //}else
      //  Zend_Session::namespaceUnset('step');
        
        $this->view->imgs = $imgs;
        $this->view->enableAdd = $enableAdd;
//        $this->view->form = $form;
//        $this->view->stateBusinessman = !empty($auth->empActive)?true:false;
        $this->view->stateCompetitor = ($stateP == 1 || $stateP == 2) ? true:false;
        $this->view->nameCompetitor = strtoupper($dataParticipante['nombre']);
        $this->view->lastnameCompetitor = strtoupper($dataParticipante['apellidos']);
    }
}

