<?php

class Office_FuxionController extends Core_Controller_ActionOffice {

    public function init() {
        /* Initialize action controller here */
        parent::init();
    }

    public function indexAction() {
        $mVideo = new Admin_Model_Video();
        $this->view->video = $mVideo->findAllByType($this->_config['app']['video']['fuxion'], true);
        $mBlog = new Admin_Model_Blog();
        $this->view->blogs = $mBlog->findAll();
        $businessman = $this->_businessman;
        $mQuiz = new Admin_Model_Quiz();
        $Estado = $mQuiz->getQuizActivo();
        $mQuizBusiness = new Admin_Model_QuizBusiness();
        $resultado = $mQuizBusiness->getQuizEmpresario($businessman['codempr'], $Estado[0]['idencuesta']);
        $result = $mQuiz->findAllByType();
        $valor = array();
        for ($i = 0; $i < count($result); $i++) {
            $valor[$i]['alternativa'] = $result[$i]['alternativa'];
            $valor[$i]['cantidad'] = $result[$i]['cantidad'];
            if ($result[$i]['cantidad'] == null) {
                $valor[$i]['porcentaje'] = '0 %';
            } else {
                $valor[$i]['porcentaje'] = round((($result[$i]['cantidad'] * 100) / $result[$i]['encuestados']) * 100) / 100 . ' %';
            }
            $valor[$i]['idtencuestaalr'] = $result[$i]['idtencuestaalr'];
        }
        $mFile = new Admin_Model_File();
        $mFileType = new Admin_Model_FileType();
        $cod = $mFileType->getCodAll();
        $datos = array();
        for ($i = 0; $i < count($cod); $i++) {
            $valora = $mFile->findAll($cod[$i]['codtfile']);
            for ($y = 0; $y < count($valora); $y++) {
                $valora[$y]['url'] = DINAMIC_URL . 'file/' . $valora[$y]['codproy'] . '/' . $valora[$y]['nombre'];
                if ($valora[$y]['extfile'] == 'pdf') {
                    $valora[$y]['extfile'] = 'pdf';
                } else {
                    $valora[$y]['extfile'] = 'img';
                }
            }
            $index= $cod[$i]['nombre'];
            $datos[$index] = $valora;
//            $msg = "File correctos.";
//            $state = 1;
        }
        $return = $datos;
        $this->addYosonVar('discounts', Zend_Json_Encoder::encode($return), false);
        $this->addYosonVar('urlCalendar',$this->selectCalendar(
            $this->_businessman['sigpais']));
        $this->view->pregunta = $result[0]['pregunta'];
        $this->view->empresarioResultado = $resultado;
        $this->view->encuesta = $valor;
        $this->view->blogs = $mBlog->findAll();
               
    }

    public function fileAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $mFile = new Admin_Model_File();
        $mFileType = new Admin_Model_FileType();
        $cod = $mFileType->getCodAll();
        $datos = array();
        for ($i = 0; $i < count($cod); $i++) {
            $valora = $mFile->findAll($cod[$i]['codtfile']);
            for ($y = 0; $y < count($valora); $y++) {
                $valora[$y]['url'] = DINAMIC_URL . 'file/' . $valora[$y]['codproy'] . '/' . $valora[$y]['nombre'];
                if ($valora[$y]['extfile'] == 'pdf') {
                    $valora[$y]['extfile'] = 'pdf';
                } else {
                    $valora[$y]['extfile'] = 'img';
                }
            }
            $datos[] = array(
                $cod[$i]['nombre'] => $valora,
            );
            $msg = "File correctos.";
            $state = 1;
        }
        $return = array(
            'state' => $state,
            'msg' => $msg,
            'DatosFile' => $datos,
        );

        $this->getResponse()
                ->setHttpResponseCode(200)
                ->setHeader('Content-type', 'application/json; charset=UTF-8', true)
                ->appendBody(json_encode($return));
    }

    public function listAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $valorEncuesta = array();
        $id = $this->getParam('idalternativa');
        if (!empty($id)) {
            $mQuizType = new Admin_Model_QuizType();
            $QuizType = $mQuizType->getQuizAlterAll($id);
            $mQuizBusiness = new Admin_Model_QuizBusiness();
            $businessman = $this->_businessman;
            $resultado = $mQuizBusiness->getQuizEmpresario($businessman['codempr'], $QuizType['tencuesta']);
            if ($resultado == false) {
                $mQuiz = new Admin_Model_Quiz();
                $mQuizType->insertQuizTypeCount($id, $QuizType['cantidad']);
                $valor = $mQuiz->getQuizAlterAll($QuizType['tencuesta']);
                $mQuiz->insertQuizCount($QuizType['tencuesta'], $valor['encuestados']);
                $mQuizBusiness->insertQuizEmpresarioCount($QuizType['tencuesta'], $businessman['codempr']);
                $result = $mQuiz->findAllByType();
                for ($i = 0; $i < count($result); $i++) {
                    if ($result[$i]['cantidad'] == null) {
                        $valorEncuesta[$i]['porcentaje'] = '0 %';
                    } else {
                        $valorEncuesta[$i]['porcentaje'] = round((($result[$i]['cantidad'] * 100) / $result[$i]['encuestados']) * 100) / 100 . ' %';
                    }
                    $valorEncuesta[$i]['alternativa'] = $result[$i]['alternativa'];
                    $valorEncuesta[$i]['cantidad'] = $result[$i]['cantidad'];
                    $valorEncuesta[$i]['idtencuestaalr'] = $result[$i]['idtencuestaalr'];
                }
                $msg = "Se agregó la encuesta.";
                $state = 1;
            } else {
                $msg = "Usted ya participó en la encuesta.";
                $state = 0;
            }
        } else {
            $msg = "No se pudo ingresar la encuesta.";
            $state = 0;
        }
        $return = array(
            'state' => $state,
            'msg' => $msg,
            'DatosEncuesta' => $valorEncuesta,
        );

        $this->getResponse()
                ->setHttpResponseCode(200)
                ->setHeader('Content-type', 'application/json; charset=UTF-8', true)
                ->appendBody(json_encode($return));
    }
    public function selectCalendar($codPais=null)
    {               
        $colPais=array('PER'=>'peru.fxbiotech','BOL'=>'bolivia.fuxion'
            ,'CHL'=>'chile.fxbiotech','COL'=>'colombia.fxbiotech','CRI'=>'costarica.fuxion'
            ,'ECU'=>'ecuador.fuxion','MEX'=>'mexico.fxbiotech','PAN'=>'panama.fxbiotech'
            ,'USA'=>'usa.fxbiotech','VEN'=>'venezuela.fxbiotech');
        
        $email=isset($colPais[$codPais])?$colPais[$codPais] : $colPais['PER'];
        return 'https://www.google.com/calendar/feeds/'.$email.'%40gmail.com/public/basic';
        
       
    }
}

