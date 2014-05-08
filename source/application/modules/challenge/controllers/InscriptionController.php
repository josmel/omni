<?php

class Challenge_InscriptionController extends Core_Controller_ActionChallenge
{

    public function init()
    {
        parent::init();
        $this->_helper->layout->setLayout('layout-challenge');
    }

    public function preDispatch() 
    {
        parent::preDispatch();
        $action = $this->getRequest()->getActionName();
        $auth = new Zend_Session_Namespace('authDesafio');
//        Zend_Debug::dump($auth->auth);exit;
        if(!empty($auth->auth) /*&& ($auth->empActive == true)*/){
            $tblParticipantes = new Challenge_Model_Participantes();
            $tblCicloParticpantes = new Challenge_Model_CicloParticipantes();
            $dataParticipante = $tblParticipantes->findRowByCodemp($auth->auth['codemp'], true);
//            Zend_Debug::dump($dataParticipante);exit;
            if($dataParticipante != false){
                $dataCicloParticipante = $tblCicloParticpantes->findRowByIdPartiIdciclo($dataParticipante['idparti'], $auth->ciclo['idciclo'], true);
                if($dataCicloParticipante != false && $action != 'step-three'){
    //                exit;
                    $this->_redirect('/');
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
        $authDesafio = new Zend_Session_Namespace('authDesafio');
        $this->view->stateBusinessman = !empty($authDesafio->empActive)?true:false;
        if (isset($step->finished) && $step->finished) $this->_redirect('/inscription/step-two-b');
    }

    public function stepOneAAction()
    {
        $step = new Zend_Session_Namespace('step');
        $authDesafio = new Zend_Session_Namespace('authDesafio');
        
        if(empty($authDesafio->empActive)) $this->_redirect('/inscription/');
        
        if($this->_request->isPost()){
            $params = $this->getAllParams();
            $auth = $authDesafio->auth;
            $validateData = (count($params)> 0)? TRUE: FALSE;
            $stepActive = (count($params)> 0)? TRUE: FALSE;            
            foreach ($params as $value) {
                if(!isset($value)){
                    $validateData = FALSE;
                    $stepActive = FALSE;
                    $step->oneB = array('active' => FALSE);
                    $step->oneC['active'] = FALSE;
                    $step->twoA['active'] = FALSE;
                    $step->twoB['active'] = FALSE;
                    $step->save['active'] = FALSE;
                    $step->three['active'] = FALSE;
                    break;
                }
            }
            $params['active'] = $stepActive;
            $step->oneA = $params;         
            
            if(!isset($step->oneA['minWeight']) && !isset($step->oneA['maxWeight'])){
                $bStats = new Core_Utils_BodyStats();
                $bodyFrameData = $bStats->corporalData($params['peso'],$params['talla'],
                    $params['sexempr'],$params['idtipmusc']);
                $step->oneA['minWeight']=$bodyFrameData['minWeight'];
                $step->oneA['maxWeight']=$bodyFrameData['maxWeight'];
            }
            
            if($validateData){
                if (isset($step->finished) && $step->finished){                 
                }                
                $this->_redirect('/inscription/step-one-b');
            }
        }
        
        $form =  new Challenge_Form_StepOneA();
        $genero=($this->_identity['sexemp']=='M')?'M' : 'F' ;        
        $form->getElement('sexempr')->setValue($genero);                
        if(isset($step->oneA)){            
            $form->populate($step->oneA);
        }
//        Zend_Debug::dump($step->oneA);exit;
        $name=$nombreEmpr=explode(' ',trim($this->_identity['name']));
        $this->view->name=$name[0];
        $this->view->form = $form;
    } 
    
    public function stepOneBAction()
    {
        $step = new Zend_Session_Namespace('step');
        $bStats = new Core_Utils_BodyStats();
        $dataStepOneA = isset($step->oneA)?$step->oneA:null;        
//        var_dump($dataStepOneA['idtipmusc']);exit;
        $weight = $dataStepOneA['peso'];
        $height = $dataStepOneA['talla'];
        $muscleMass = $dataStepOneA['idtipmusc'];        
        $fatPer = $dataStepOneA['indgrasa'];                                
        $imcData = $bStats->imcData($weight, $height,$dataStepOneA['sexempr']);       
        $pgcData = $bStats->calcPGC($dataStepOneA);
        $igcData = false;
        if($pgcData){            
            $igcData = $bStats->calcIGC($pgcData,$dataStepOneA['sexempr']);
        }
        $bodyFrameData = $bStats->corporalData($weight,$height,$dataStepOneA['sexempr'],$muscleMass);        
        if(empty($bodyFrameData)){
            $this->_flashMessenger->error("Datos fuera de rango");
            $this->redirect ('/inscription/step-one-a');            
        }
        $bodyFrameData['indGrasa']=$dataStepOneA['indgrasa'];
        $name=$nombreEmpr=explode(' ',trim($this->_identity['name']));
        $this->view->name=$name[0];
        $step->oneA['minWeight']=$bodyFrameData['minWeight'];
        $step->oneA['maxWeight']=$bodyFrameData['maxWeight'];
        $this->view->bodyFrameData = $bodyFrameData;      
        $this->view->imcData = $imcData;
        $this->view->pgc = $pgcData;                
        $this->view->igcData = $igcData;        
        $this->view->weight = $weight;        
        if(!empty($dataStepOneA) && $dataStepOneA['active']){
            $step->oneB = array('active' => TRUE);
        }else{
            $this->_redirect('/inscription/step-one-a'); exit;
        }
    }
    
    public function stepOneCAction()
    {
        $step = new Zend_Session_Namespace('step');
        $dataStepOneB = isset($step->oneB)?$step->oneB:null;
        if(!empty($dataStepOneB) && $dataStepOneB['active'] && $this->_request->isPost()){          
//            if($this->_request->isPost()){
                $params = $this->getAllParams();
    //            Zend_Debug::dump($params);exit;
                $validateData = (count($params)> 0)? TRUE: FALSE;
                $stepActive = (count($params)> 0)? TRUE: FALSE;
                foreach ($params as $value) {
                    if(empty($value)){
                        $validateData = FALSE;
                        $stepActive = FALSE;
                        $step->twoA['active'] = FALSE;
                        $step->twoB['active'] = FALSE;
                        $step->save['active'] = FALSE;
                        $step->three['active'] = FALSE; 
                        break;
                    }
                }
                $params['active'] = $stepActive;
                $step->oneC = $params;
                if($validateData){
                    if (isset($step->finished) && $step->finished) $this->_redirect('/inscription/step-two-b');
                    $this->_redirect('/inscription/step-two-a');exit;
                }
                $form =  new Challenge_Form_StepOneC();
                if(isset($step->oneC)){
                    $form->populate($step->oneC);
                }
                $this->view->form = $form;
//            }   
        }elseif(!empty($dataStepOneB) && $dataStepOneB['active']){            
            $bodyFrameData=$step->oneA;            
            $form =  new Challenge_Form_StepOneC();
            $colKg=$this->setArrayKg($bodyFrameData['minWeight'],$bodyFrameData['maxWeight']);
            $element=$this->setSelectForm($colKg);
            $form->addElement($element);     
            
            if(isset($step->oneC)){
                $form->populate($step->oneC);
            }
            $this->view->bodyFrameData=$bodyFrameData;
            $this->view->form = $form;
        }else{
            $this->_redirect('/inscription/step-one-a');exit;
        }
    }
    
    public function stepTwoAAction()
    {
        $step = new Zend_Session_Namespace('step');
        $dataStepOneC = isset($step->oneC)?$step->oneC:null;
        $dataStepUpload = isset($step->upload)?$step->upload:null;
        
        
        if(!empty($dataStepOneC) && $dataStepOneC['active'] && $this->_request->isPost()){
            $params = $this->getAllParams();
//            Zend_Debug::dump($params);exit;
            $validateData = (count($params)> 0)? TRUE: FALSE;
            $stepActive = (count($params)> 0)? TRUE: FALSE;
            unset($params['a']);
            foreach ($params as $key => $value) {
                if(empty($value) && $key != 'rpm' && $key != 'codefcp'){
                    $validateData = FALSE;
                    $stepActive = FALSE;
                    $step->twoB['active'] = FALSE;
                    $step->save['active'] = FALSE;
                    $step->three['active'] = FALSE;
                    break;
                }
            }
            $params['active'] = $stepActive;
            $step->twoA = $params;
            
            if($validateData && !empty($dataStepUpload) && $dataStepUpload['active']){
                $idMentor = $params['codmentor'];
                $mBusinessman = new Businessman_Model_Businessman();
                $dataMentor = $mBusinessman->findDataViewById($idMentor);
                
                $step->twoA['dataMentor'] = array();
                $step->twoA['dataMentor']['nomemp'] = $dataMentor['nomempr'];
                $step->twoA['dataMentor']['apepaterno'] = $dataMentor['appempr'];
                $step->twoA['dataMentor']['apematerno'] = $dataMentor['apmempr'];
                $step->twoA['dataMentor']['telefono'] = $dataMentor['telefono'];
                $step->twoA['dataMentor']['email'] = $dataMentor['emaempr'];
                $step->twoA['dataMentor']['celular'] = $dataMentor['celular'];
                $step->twoA['dataMentor']['codefcp'] = $dataMentor['codempr'];
                
                $this->_redirect('/inscription/step-two-b');exit;
            }
            $form =  new Challenge_Form_StepTwoA();
            if(isset($step->twoA)){
                $form->populate($step->twoA);
            }
            $this->view->form = $form;
        }elseif(!empty($dataStepOneC) && $dataStepOneC['active']){
            $form =  new Challenge_Form_StepTwoA();
            
            if(isset($step->twoA)){
                $form->populate($step->twoA);
            } else {
                $mBusinessman = new Businessman_Model_Businessman();
                $businessman = $mBusinessman->findDataViewById($this->_identity['codemp']);
                
                $step->twoA['nomemp'] = $businessman['nomempr'];
                $step->twoA['apepaterno'] = $businessman['appempr'];
                $step->twoA['apematerno'] = $businessman['apmempr'];
                $step->twoA['telefono'] = $businessman['telefono'];
                $step->twoA['email'] = $businessman['emaempr'];
                $step->twoA['rpm'] = $businessman['celular'];
                $step->twoA['codefcp'] = $businessman['codempr'];
                $form->populate($step->twoA);
            }
            $this->view->form = $form;
        }else{
            $this->_redirect('/inscription/step-one-c');exit;
        }
        $this->view->upload = $dataStepUpload;
    }    
    
    
    public function stepTwoBAction()
    {
        $step = new Zend_Session_Namespace('step');
        $dataStepTwoA = isset($step->twoA)?$step->twoA:null;
        $dataStepUpload = isset($step->upload)?$step->upload:null;        
        $authDesafio = new Zend_Session_Namespace('authDesafio');
        $ciclo = $authDesafio->ciclo;
        
        //var_dump($ciclo);//      
        if(!empty($dataStepTwoA) && $dataStepTwoA['active'] && !empty($dataStepUpload) && $dataStepUpload['active']){
            $step->twoB = array('active' => TRUE);
            if($this->_request->isPost()){
                $this->_redirect('/inscription/save');exit;
            }
            $step->finished = true;
            $this->view->step = $step;
            $this->view->sexo=($step->oneA['sexempr']=='F')?'Femenino':'Masculino';
//            Zend_Debug::dump($step->oneA);            
            $this->view->ciclo = $ciclo;
        }else{
            $this->_redirect('/inscription/step-two-a');exit;
        }
    }     
    
    public function stepThreeAction()
    {
        $step = new Zend_Session_Namespace('step');
        $bStats = new Core_Utils_BodyStats();
        $dataStepSave = isset($step->save)?$step->save:null;
        if(!empty($dataStepSave) && $dataStepSave['active']){
            $step->three = array('active' => TRUE);
            
            $name=$nombreEmpr=explode(' ',trim($this->_identity['name']));
            $this->view->name=$name[0];
            
            $kcal=  Core_Utils_BodyStats::definedAlimentationXHeight($step->oneA['talla']);                        
            $this->view->kcal = $kcal;
            
            $path = '/static/alimentacion/btn_1/';
            $img = Core_Utils_BodyStats::colectionPathPdfAndPicture($path);
            
            $imgKcal = $img[$kcal];
            $this->view->imgKcal = $imgKcal;
            
        }else{
            $this->_redirect('/inscription/step-two-b');exit;
        }
    }
    
    public function saveAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true); 
        $step = new Zend_Session_Namespace('step');
        $auth = new Zend_Session_Namespace('authDesafio');
        $dataStepTwoB = isset($step->twoB)?$step->twoB:null;
//        Zend_Debug::dump($dataStepTwoB);exit;
        if(!empty($dataStepTwoB) && $dataStepTwoB['active'] /*&& $this->_request->isPost()*/){
            //falta la logica del model
            
//            Zend_Debug::dump(Zend_Session::namespaceGet('step'));
//            Zend_Debug::dump(Zend_Session::namespaceGet('authDesafio'));
            
            $saveHelper = $this->getHelper('saveInscription');
            if ($saveHelper->saveInscription($auth, $step)) { 
                $step->save = array('active' => TRUE);
                $mBusinessman = new Businessman_Model_Businessman();
                $mBusinessman->updateChallenge($this->_identity['codemp'], $step->twoA);
                
                $mBusinessmanContact = new Businessman_Model_BusinessmanContact();
                $dataContact = array('telefono' => $step->twoA['telefono'], 
                    'celular' => $step->twoA['rpm']);
                $mBusinessmanContact->updateFromChallenge($this->_identity['codemp'], $dataContact);
                $this->_redirect('/inscription/step-three'); exit;
                //$this->_redirect('/'); exit;
            } else {
                echo "Ocurrió un error al guardar la información."; exit;
            }
        }else{
            $this->_redirect('/inscription/step-two-b');exit;
        }
    }
    
    public function setArrayKg($weightMin,$weightMax)
    {
        $setVal=array();
        for($i=round($weightMin,0,PHP_ROUND_HALF_DOWN);
            $i<=round($weightMax,0,PHP_ROUND_HALF_DOWN);$i++){
            $setVal[$i]=$i;
        }
        return $setVal;
    }
    /**
     * 
     * @param Objt $form
     */
    public function setSelectForm($array)
    {         
        $e = new Zend_Form_Element_Select('kilobaja');    
        $e->setMultiOptions($array);
        $e->setAttrib('class','slc-small');
        $e->removeDecorator('Label');
        $e->removeDecorator('DtDdWrapper');          
        $e->removeDecorator('HtmlTag');
        $e->removeDecorator('Errors');
        return $e;
    }
}