<?php
class Admin_QuizController extends Core_Controller_ActionAdmin {
    
    public function init() {
        parent::init();
    }

    public function indexAction() {
    }
    
    public function listAction() {
    	$this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true); 
        $params=$this->_getAllParams();
        $iDisplayStart=isset($params['iDisplayStart'])?$params['iDisplayStart']:null;
        $iDisplayLength=isset($params['iDisplayLength'])?$params['iDisplayLength']:null;
        $sEcho=isset($params['sEcho'])?$params['sEcho']:1;     
        $sSearch=isset($params['sSearch'])?$params['sSearch']:'';  
        $obj=new Application_Entity_DataTable('Quiz',0,$sEcho, false);
        $obj->setIconAction($this->action());
        $query="";
        $query.=!empty($sSearch)? " AND pregunta like '%".$sSearch."%' ":" ";
        $obj->setSearch($query);
        $this->getResponse()            
	     	->setHttpResponseCode(200)
            ->setHeader('Content-type', 'application/json;charset=UTF-8', true)
            ->appendBody(json_encode($obj->getQuery($iDisplayStart,$iDisplayLength)));   
    }

    public function newAction() {
        $this->_helper->viewRenderer->setNoRender(true);         
        $form = new Admin_Form_Quiz();
        $obj = new Application_Entity_RunSql('Quiz');      
        if ($this->_request->isPost()) {
            $dataForm = $this->_request->getPost();
            try{
                $msj=array();  
                $mEncuesta = new Admin_Model_QuizType();
                if(empty($dataForm['idencuesta'])){
                    $dataForm['tmsfeccrea'] = date('Y-m-d H:i:s');
                    $dataForm['vchusucrea'] = $this->_identity->iduser;
                    $obj->save=$dataForm;
                    $mEncuesta->insertQuizType($obj->save,$dataForm['alternativa']);
                }else{
                    $dataForm['tmsfecmodif'] = date('Y-m-d H:i:s');
                    $dataForm['vchusumodif'] = $this->_identity->iduser;
                    $obj->edit=$dataForm;
                    $mEncuesta->updateQuizType($dataForm['idtencuestaalr'],$dataForm['alternativa']);
                }
                $this->_redirect('/quiz');
            }catch (Exception $e){
                echo $e->getMessage();
            }
        }else{
            $this->view->titulo="Nueva Encuesta";
            $this->view->submit="Grabar Encuesta";
            $this->view->action="/quiz/new";
            $form->addDecoratorCustom('forms/_formQuiz.phtml');  
            echo $form;
        }
    }
    
    public function editAction() {
        $this->_helper->viewRenderer->setNoRender(true);     
        $id=$this->_getParam('id',0);
        $form=new Admin_Form_Quiz();
        $objType = new Admin_Model_QuizType();
        $QuizType = $objType->getPairsAll($id);
        if(!empty($id)){
            $obj=new Application_Entity_RunSql('Quiz');
            $obj->getone=$id;
            $dataObj =$obj->getone;
            $form->populate($dataObj);
        }
        $this->view->QuizType=$QuizType;
        $this->view->titulo="Editar Encuesta";
        $this->view->submit="Guardar Encuesta";
        $this->view->action="/quiz/new";
        $form->setDecorators(array(array('ViewScript',
            array('viewScript'=>'forms/_formQuiz.phtml'))));  
        echo $form;
    }
    public function deleteAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true); 
        $id=$this->getParam('id');
        $rpta=array();
        if(!empty($id)){
            try{
                $obj=new Application_Entity_RunSql('Quiz');
                $obj->edit=array('vchestado'=>'D',$obj->getPK()=>$id); 
                $rpta['msj']='ok';
            }  catch (Exception $e){
                $rpta['msj']=$e->getMessage();
            }
        }else{
            $rpta['msj']='faltan datos';
        }
         $this->getResponse()            
            ->setHttpResponseCode(200)
            ->setHeader('Content-type', 'application/json; charset=UTF-8', true)
            ->appendBody(json_encode($rpta));
    }
    
    function action()
    {
       $action="<a class=\"tblaction ico-edit\" title=\"Editar\" href=\"/quiz/edit/id/__ID__\">Editar</a>
                    <a data-id=\"__ID__\" class=\"tblaction ico-delete\" title=\"Eliminar\"  href=\"/quiz/delete\">Eliminar</a>";
       return $action;
    }
}

