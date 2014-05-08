<?php

class Challenge_IndexController extends Core_Controller_ActionChallenge
{
    public function init() {
        parent::init();
        $this->_helper->layout->setLayout('layout-challenge');
    }

    public function preDispatch() 
    {
        parent::preDispatch();
        
//        $idCiclo = $this->_getParam('idCiclo', 1);
        
        $authDesafio = new Zend_Session_Namespace('authDesafio');
        $auth = $authDesafio->auth;
//        Zend_Debug::dump(Zend_Session::namespaceGet('authDesafio'));exit;
//        $authDesafio->idCiclo = $idCiclo;

        $tblCiclo = new Challenge_Model_Ciclo();
        
//        echo ((strtotime($authDesafio->ciclo['fecfin'])-strtotime($authDesafio->ciclo['fecini']))/(60 * 60 * 24)) .'<br>';exit;
        
        
        if(!empty($authDesafio->auth) /*&& ($authDesafio->empActive == true)*/){
            if ($this->_getParam('session', false) == false) 
                return;
            
            //$authDesafio->empActive = true;
            $authDesafio->isParticipante = false;
            $authDesafio->idParticipante = null;
        
            $tblParticipantes = new Challenge_Model_Participantes();
            $tblCicloParticpantes = new Challenge_Model_CicloParticipantes();
            
            $dataParticipante = $tblParticipantes->findRowByCodemp($auth['codemp'], true);
            if(($dataParticipante == false)){
                $authDesafio->isParticipante = FALSE;
                $dataCiclo = $tblCiclo->findCicloActivo('A');
                $authDesafio->ciclo = $dataCiclo;
                $this->_redirect('/inscription');
            }else{
                $authDesafio->idParticipante = $dataParticipante['idparti'];
                $authDesafio->isParticipante = TRUE;
                $dataCiclo = $tblCicloParticpantes->getActiveCycleByComepetitor($dataParticipante['idparti']);
                $authDesafio->ciclo = $dataCiclo;
                
                //var_dump($dataCiclo); exit;
                //update 
                $tblParticipantes->update(array('estadoofi' => $auth['officeActive'] ? 1 : 0), $dataParticipante['idparti']);
                $dataCicloParticipante = $tblCicloParticpantes->findRowByIdPartiIdciclo(
                        $dataParticipante['idparti'], $authDesafio->ciclo['idciclo'], true);
                if($dataCicloParticipante == false){
                    $this->_redirect('/inscription');
                }else{
                }
            }
        }else{
//            if(APPLICATION_ENV == 'local'){
//                $this->_redirect('http://local.office/');
//            }else{
//                $this->_redirect('http://fuxion.onlinestudioproductions.com');
//            }
            $this->_redirect($this->_config['websites']['office']);
        }
    }
    
    public function postDispatch() {
        parent::postDispatch();
    }
    
    public function indexAction()
    {
        
    }
    
    public function conditionAction()
    {
        $auth = new Zend_Session_Namespace('authDesafio');
        $tblParticipante =  new Challenge_Model_Participantes();
        $tblDetaCiclo = new Challenge_Model_DetaCiclo();
        $tblCicloParticpantes = new Challenge_Model_CicloParticipantes();
        
        $dataParticipante = $tblParticipante->findRowByCodemp($auth->auth['codemp'], true);        
        $dataCicloParticipante = $tblCicloParticpantes->findRowByIdPartiIdciclo(
            $dataParticipante['idparti'], $auth->ciclo['idciclo'], true);
        $dataIniDetaCiclo = $tblDetaCiclo->getRowMeasure(
            $dataCicloParticipante['idcicparti'], 'ini', true);        
        $dataFinDetaCiclo = $tblDetaCiclo->getRowMeasure(
            $dataCicloParticipante['idcicparti'], 'fin', true);              
        $stateCompetitorHelper = $this->getHelper('stateCompetitor');
        $stateP = $stateCompetitorHelper->stateCompetitor(
            $dataFinDetaCiclo['fecfin'],$dataParticipante['vchestado']);                
        $dataDC = $tblDetaCiclo->findAllByIdCicParti(
            $dataCicloParticipante['idcicparti'], true);
        $percentageCompetitor = ( ( (count($dataDC) - 1) * 100 ) / ($auth->ciclo['nrosemana']) );
        
        
        
        $this->view->participante = $dataParticipante;
        $this->view->detalleiniciclo = $dataIniDetaCiclo;
        $this->view->estadoParticipante = $stateP;
        $this->view->detallefinciclo = $dataFinDetaCiclo;
        $this->view->percentageCompetitor = $percentageCompetitor;
    }

    public function consumeAction()
    {
        $auth = new Zend_Session_Namespace('authDesafio');
        $this->view->empActivo = $auth->empActive;
        
        $tblParticipante =  new Challenge_Model_Participantes();
        $tblDetaCiclo = new Challenge_Model_DetaCiclo();
        $tblCicloParticpantes = new Challenge_Model_CicloParticipantes();
        $dataParticipante = $tblParticipante->findRowByCodemp($auth->auth['codemp'], true);
        $dataCicloParticipante = $tblCicloParticpantes->findRowByIdPartiIdciclo(
                $dataParticipante['idparti'], $auth->ciclo['idciclo'], true);
        $dataFinDetaCiclo = $tblDetaCiclo->getRowMeasure(
                $dataCicloParticipante['idcicparti'], 'fin', true);
        
        $stateCompetitorHelper = $this->getHelper('stateCompetitor');
        $stateP = $stateCompetitorHelper->stateCompetitor($dataFinDetaCiclo['fecfin'],$dataParticipante['vchestado']);
        $this->view->estadoParticipante = $stateP;
    }
    
    public function progressAction()
    {
        $auth = new Zend_Session_Namespace('authDesafio');
        $step = new Zend_Session_Namespace('step');
        $form = new Challenge_Form_StepOneA();
        
        $tblParticipante =  new Challenge_Model_Participantes();
        $tblCicloParticpantes = new Challenge_Model_CicloParticipantes();
        $tblDetaCiclo = new Challenge_Model_DetaCiclo();
        
        $dataParticipante = $tblParticipante->findRowByCodemp($auth->auth['codemp'],true);
        $dataCicloParticipante = $tblCicloParticpantes->findRowByIdPartiIdciclo(
            $dataParticipante['idparti'],$auth->ciclo['idciclo'], true);
        $dataIniDetaCiclo = $tblDetaCiclo->getRowMeasure(
            $dataCicloParticipante['idcicparti'], 'ini', true);
        $dataFinDetaCiclo = $tblDetaCiclo->getRowMeasure(
            $dataCicloParticipante['idcicparti'], 'fin', true);
        
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
        $stateP = $stateCompetitorHelper->stateCompetitor($dataFinDetaCiclo['fecfin'],
            $dataParticipante['vchestado']);
        $this->view->estadoParticipante = $stateP;
        
        if($this->_request->isPost()){
            $allParams = $this->getAllParams();
            $dataStepUpload = isset($step->upload)?$step->upload:null;
            
//            $validateInsert = $tblDetaCiclo->validateInsert(date('Y-m-d H:i:s'), 
//                    $dataCicloParticipante['idcicparti'], true);
            if ($form->isValidPartial($allParams) && !empty($dataStepUpload) && 
                $dataStepUpload['active'] && $auth->empActive && 
                $dataParticipante['vchestado'] == 'A' &&
                $dataCicloParticipante['vchestado'] == 'A' && $enableAdd) {
                // elements present all passed validations
//                exit;
                $dataDetaCiclo = $allParams;
                $dataDetaCiclo['idcicparti'] = $dataCicloParticipante['idcicparti'];
                $dataDetaCiclo['idtipmusc'] = $dataFinDetaCiclo['idtipmusc'];
                $dataDetaCiclo['fotowincha'] = $step->upload['wincha'];
                $dataDetaCiclo['fotofrente'] = $step->upload['frente'];
                $dataDetaCiclo['fotoperfil'] = $step->upload['perfil'];
                $dataDetaCiclo['fotootros'] = $step->upload['otro'];
                $dataDetaCiclo['semana'] = $numWeek;
                $dataDetaCiclo['fecini'] = $fechaIniDC;
                $dataDetaCiclo['fecfin'] = $fechaFinDC;
                $dataDetaCiclo['vchestado'] = 1;
                $dataDetaCiclo['vchusucrea'] = $auth->auth['codemp'];
                $dataDetaCiclo['tmsfeccrea'] = date('Y-m-d H:i:s');
                $saveDetaCiclo = $tblDetaCiclo->insert($dataDetaCiclo);
                Zend_Session::namespaceUnset('step');
                $this->_redirect('/index/progress');
//                exit;
            } else {
                // one or more elements tested failed validations
            }
        }    
        
        $details = $tblDetaCiclo->findAllByIdCicParti($dataCicloParticipante['idcicparti']);
//        Zend_Debug::dump($details);Exit;
        $talla = null;
        if(count($details) > 0){
            $talla = $details[0]['talla'];
        }
        
        $this->view->talla = $talla;
        
        $arrayX = array();
        $arrayY = array();
        for($i = 1; $i <= $auth->ciclo['nrosemana']; $i++) {
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
                $itemImgs['fotowincha'] = DINAMIC_URL.$path.$detail['fotowincha'];
            if(!empty($detail['fotofrente'])) 
                $itemImgs['fotofrente'] = DINAMIC_URL.$path.$detail['fotofrente'];
            if(!empty($detail['fotoperfil'])) 
                $itemImgs['fotoperfil'] = DINAMIC_URL.$path.$detail['fotoperfil'];
            if(!empty($detail['fotootros'])) 
                $itemImgs['fotootros'] = DINAMIC_URL.$path.$detail['fotootros'];
            
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
        $this->view->form = $form;
        $this->view->stateBusinessman = !empty($auth->empActive)?true:false;
        $this->view->stateCompetitor = ($stateP == 1 || $stateP == 2) ? true:false;
    }
    
    public function mentorAction()
    {
        $auth = new Zend_Session_Namespace('authDesafio');
        $mBusinessman = new Businessman_Model_Businessman();
        $tblParticipante =  new Challenge_Model_Participantes();
        $tblMentor = new Challenge_Model_Mentor();
        $tblCicloParticpantes = new Challenge_Model_CicloParticipantes();
        $dataParticipante = $tblParticipante->findRowByCodemp($auth->auth['codemp'], true);
        $dataCicloParticipante = $tblCicloParticpantes->findRowByIdPartiIdciclo($dataParticipante['idparti'], $auth->ciclo['idciclo'], true);
        $dataMentor = $tblMentor->findRowByIdMentor($dataCicloParticipante['idmentor'], true);
        $dataMentor['businessman'] = $mBusinessman->findDataViewById($dataMentor['codemp']);
        //var_dump($dataMentor);
        $this->view->mentor = $dataMentor;
    }
    
    public function contactAction()
    {
        if($this->_request->isPost()){
            $params = $this->getAllParams();
            $auth = new Zend_Session_Namespace('authDesafio');
            $tblContacto = new Challenge_Model_Contacto();
            $dataSaveContacto = array(
                'nomconta' => $params['nombre'],
                'emailconta' => $params['correo'],
                'consulta' => $params['mensaje'],
                'vchestado' => 1,
                'vchusucrea' => $auth->auth['codemp'],
                'tmsfeccrea' => date('Y-m-d H:i:s'),
            );
            $tblContacto->insert($dataSaveContacto);
            
            $mailHelper = $this->getHelper('mail');
            $sendMail = array(
                'name' => $params['nombre'],
                'email' => $params['correo'],
                'message' => 'Gracias por contactarnos, en breve nos comunicaremos con usted.',
            );

            $dataMailing = array(            
                'to' => 'retofuxion@fuxion.net',
                'data' => Zend_Json_Encoder::encode($sendMail)
            );

            $sendMail['dataMailing'] = $dataMailing;            
            $sendMail['to'] = 'retofuxion@fuxion.net';

            $mailHelper->contactChallenge($sendMail);
            $this->redirect('/index/succes-contact');
        }       
    }
    
    public function succesContactAction()
    {
        
    }
    
    public function diagnosticAction()
    {
        
        $bStats = new Core_Utils_BodyStats();
        $participante = new Challenge_Model_Participantes();
        $dataPart = $participante->findRowByCodemp($this->_identity['codemp']);                
        $cicloParticipantes = new Challenge_Model_CicloParticipantes();
        $dataFatPerson = $cicloParticipantes->dataFatPart($dataPart['idparti']);
//        var_dump(Core_Utils_BodyStats::contextura($dataFatPerson['indgrasa']));exit;
        $weight=$dataFatPerson['peso'];
        $height=$dataFatPerson['talla'];
        $cuello=$dataFatPerson['cuello'];
        $cadera=$dataFatPerson['cadera'];
        $cintura=$dataFatPerson['cintura'];
        $idTipMusc=$dataFatPerson['idtipmusc'];
        $indgrasa=$dataFatPerson['indgrasa'];
        $imcData = $bStats->imcData($weight,$height,$this->_identity['sexemp']);
        $dataStepOneA=array('cintura'=>$cintura,'cadera'=>$cadera,'cuello'=>$cuello,'talla'=>$height,'sexempr'=>$this->_identity['sexemp']);
        $pgcData = $bStats->calcPGC($dataStepOneA);
        $igcData = false;
        if($pgcData){            
            $igcData = $bStats->calcIGC($pgcData,$this->_identity['sexemp']);
        }
        $bodyFrameData = $bStats->corporalData($weight,$height,
            $this->_identity['sexemp'],Core_Utils_BodyStats::contextura($idTipMusc));
        $bodyFrameData['indGrasa']=$indgrasa;
        $name=$nombreEmpr=explode(' ',trim($this->_identity['name']));
        $this->view->name=$name[0];
        $step->oneA['minWeight']=$bodyFrameData['minWeight'];
        $step->oneA['maxWeight']=$bodyFrameData['maxWeight'];
        $this->view->bodyFrameData = $bodyFrameData;      
        $this->view->imcData = $imcData;
        $this->view->pgc = $pgcData;                
        $this->view->igcData = $igcData;        
        $this->view->weight = $weight;        
    }
    
    public function nutritionAction()
    {
        $auth = new Zend_Session_Namespace('authDesafio');
        $tblParticipante = new Challenge_Model_Participantes();
        $tblDetaCiclo = new Challenge_Model_DetaCiclo();
        $tblCicloParticpantes = new Challenge_Model_CicloParticipantes();
        
        $dataParticipante = $tblParticipante->findRowByCodemp($auth->auth['codemp'], true);
        
        $dataCicloParticipante = $tblCicloParticpantes->findRowByIdPartiIdciclo(
            $dataParticipante['idparti'], $auth->ciclo['idciclo'], true);
        
        $dataFinDetaCiclo = $tblDetaCiclo->getRowMeasure(
            $dataCicloParticipante['idcicparti'], 'fin', true);  
        
        $talla = $dataFinDetaCiclo['talla'];
        
        $name=$nombreEmpr=explode(' ',trim($this->_identity['name']));
        $this->view->name=$name[0];

        $kcal = Core_Utils_BodyStats::definedAlimentationXHeight($talla);
        $this->view->kcal = $kcal;

        $path = '/static/alimentacion/btn_1/';
        $img = Core_Utils_BodyStats::colectionPathPdfAndPicture($path);

        $imgKcal = $img[$kcal];
        $this->view->imgKcal = $imgKcal;
            
//         return $this->_forward('step-three', 'inscription', 'challenge');
//        $this->render('inscription/step-three', null, true);
    }
    
    public function sendEmailAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $file = $this->_getParam('kcal', null);
        $emails = $this->_getParam('email', null);
        $typeSend=$this->getParam('type',null);
        $rutaPDF=SITE_URL.'static/alimentacion/btn_1/'.$file.'/'.$file.'.pdf';
        $params=$this->getAllParams();
        var_dump($params);       
        if(!empty($emails)){                        
            try{
                $this->sendMail($emails, $rutaPDF,$typeSend);                
            }catch(Exception $e){
                echo $e->getMessage();      
                exit;
            }
        }
        $this->_redirect('/inscription/step-three');
    }
    
    public function sendMail($emails,$routPDF,$typeSend)
    {
        $mail = new Zend_Mail();
        $config = array(                            
            'auth' => 'login', 
            'username' => 'no_reply@onlinestudioproductions.com', 
            'password' => 'ujf758Pw' 
        );        
        $rutaPDF=$this->setRout($typeSend, $routPDF);
        $transport = new Zend_Mail_Transport_Smtp('mail.onlinestudioproductions.com',$config);
//        $content = file_get_contents(APPLICATION_PATH.'/../public/static/alimentacion/btn_1/'.$file.'/'.$file.'.pdf'); // e.g. ("attachment/abc.pdf")
//        $pdf = new Zend_Mime_Part($content);
//        $attachment = $mail->createAttachment($pdf);
//        $attachment->type = 'application/pdf';
//        $attachment->disposition = Zend_Mime::DISPOSITION_ATTACHMENT;
//        $attachment->encoding = Zend_Mime::ENCODING_BASE64;
//        $attachment->filename = 'Kcal.pdf';
        $mail->setBodyHtml("Tu dieta:  descargue desde "
            . "<a href='".$rutaPDF."' >AQUI </a>para consejos y "
            . "tips que ayudaran a mejorar tú alimentacion ",'utf8');
        $mail->setFrom('no-reply@fuxion.net', 'Desafio Fuxion');        
        $mail->addTo($emails,'User');
        $mail->setSubject('Desafío fuxion ');
        $mail->send($transport);  
        
    }
    
    public function setRout($typeSend,$rutDieta)
    {
        switch($typeSend)
        {
            case 'dieta' : $route=$rutDieta;
                break;
            case 'tips' : $route=SITE_URL.'/static/alimentacion/consejos/tips.pdf';
                break;
            case 'refrigerio' : $route=SITE_URL.'/static/alimentacion/refrigerio/refrigerios.pdf';
                break;
            default : throw new Exception('Auch un error ha ocurrido');
        }
        return $route;
    }
}

