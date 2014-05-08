<?php

class Office_BusinessController extends Core_Controller_ActionOffice {

    public function init() {
        /* Initialize action controller here */
        parent::init();
    }

    public function indexAction() {
        $form = new Businessman_Form_Report($this->_businessman['codempr']);
        $picture = DINAMIC_URL . "businessman/" . $this->_config['app']['businessman']['picture']['two']
                ['height'] . 'x' . $this->_config['app']['businessman']['picture']['two']['width'] . '/' . $this->_businessman['picture'];

        if ($this->_request->isPost()) {
            $semana = $this->getParam('tsemana');
        } else {
            $objType = new Businessman_Model_Report();
            $codsema = $objType->getAffiliate($this->_businessman['codempr']);
            $idsemana = $codsema['codsema'];
            $semanas = $objType->getWeekOne($idsemana);
            $semana = $semanas['codsema'];
        }
        $this->view->iframe = $this->iframeBusiness($this->_businessman['codsession'], $this->_businessman['codempr']);
        $billerHelper = $this->getHelper('billerServices');
        $dataBusinessman = $billerHelper->getDataBusinessman(
                $this->_businessman['codempr'], $semana, $this->_config['app']['service']['prolife']
        );
        $dataQualifiedBusinessman = $billerHelper->getQualifiedBusinessman(
                $this->_businessman['codempr'], $semana, $this->_config['app']['service']['prolife']
        );
        $dataBusinessmanIndicators = $billerHelper->getBusinessmanIndicators(
                $this->_businessman['codempr'], $semana, $this->_config['app']['service']['prolife']
        );
        $dataBusinessmanRange = $billerHelper->getBusinessmanRange(
                $this->_businessman['codempr'], $semana, $this->_config['app']['service']['prolife']
        );
        if ($dataBusinessmanRange == null) {
            $dataBusinessmanRange['binarioIzquierdo'] = 0;
            $dataBusinessmanRange['binarioDerecho'] = 0;
        } else {
            $dataBusinessmanRange['binarioIzquierdo'] = $dataBusinessmanRange['binarioIzquierdo'];
            $dataBusinessmanRange['binarioDerecho'] = $dataBusinessmanRange['binarioDerecho'];
        }

        $SubtotalIzquierdo = ($dataBusinessman['saldoInicialIzquierda'] + $dataBusinessmanRange['binarioIzquierdo']);
        $SubtotalDerecho = ($dataBusinessman['saldoInicialDerecha'] + $dataBusinessmanRange['binarioDerecho']);
        $totalIzquierdo = $SubtotalIzquierdo;
        $totalDerecho = $SubtotalDerecho + $dataBusinessman['excedenteSemanal'];
        $this->view->picture = $picture;
        $this->view->SubtotalIzquierdo = $SubtotalIzquierdo;
        $this->view->SubtotalDerecho = $SubtotalDerecho;
        $this->view->totalIzquierdo = $totalIzquierdo;
        $this->view->totalDerecho = $totalDerecho;
        $this->view->dataQualifiedBusinessman = $dataQualifiedBusinessman['result'];
        $this->view->dataBusinessmanRange = $dataBusinessmanRange;
        $this->view->data = $dataBusinessman;
        $this->view->dataIndicators = $dataBusinessmanIndicators;
        $this->view->form = $form;
    }

    public function redAction() {

        $billerHelper = $this->getHelper('billerServices');
        $dataPatrocinados = $billerHelper->getPatrocinados(
                $this->_businessman['codempr'], 1, $this->_config['app']['service']['prolife']
        );
        $returnPatrocinados = array();
        foreach ($dataPatrocinados as $value) {
            $returnPatrocinados[] = $value['codempr'];
        }
        $form = new Businessman_Form_Report($this->_businessman['codempr']);
        if ($this->_request->isPost()) {
            $empresario = $this->getParam('partners');
            $semana = $this->getParam('semana');
            $this->view->iframe = $this->iframeBusiness($this->_businessman['codsession'], $this->_businessman['codempr']);

            $dataBusinessman = $billerHelper->getDataBusinessman(
                    $empresario, $semana, $this->_config['app']['service']['prolife']
            );
            $dataBusinessmanRange = $billerHelper->getBusinessmanRange(
                    $empresario, $semana, $this->_config['app']['service']['prolife']
            );
            if ($dataBusinessmanRange == null) {
                $dataBusinessmanRange['binarioIzquierdo'] = 0;
                $dataBusinessmanRange['binarioDerecho'] = 0;
            } else {
                $dataBusinessmanRange['binarioIzquierdo'] = $dataBusinessmanRange['binarioIzquierdo'];
                $dataBusinessmanRange['binarioDerecho'] = $dataBusinessmanRange['binarioDerecho'];
            }
            $SubtotalIzquierdo = ($dataBusinessman['saldoInicialIzquierda'] + $dataBusinessmanRange['binarioIzquierdo']);
            $SubtotalDerecho = ($dataBusinessman['saldoInicialDerecha'] + $dataBusinessmanRange['binarioDerecho']);
            $totalIzquierdo = $SubtotalIzquierdo;
            $totalDerecho = $SubtotalDerecho + $dataBusinessman['excedenteSemanal'];
            $dataQualifiedBusinessman = $billerHelper->getQualifiedBusinessman(
                    $empresario, $semana, $this->_config['app']['service']['prolife']
            );
            $dataBusinessmanIndicators = $billerHelper->getBusinessmanIndicators(
                    $empresario, $semana, $this->_config['app']['service']['prolife']
            );
            $bussinesManPicture = new Businessman_Model_Businessman();
            $bussines = $bussinesManPicture->findDataViewById($empresario);
            $this->view->picture = $bussines['picture'];
            $this->view->dataQualifiedBusinessman = $dataQualifiedBusinessman['result'];
            $this->view->dataBusinessmanRange = $dataBusinessmanRange;
            $this->view->data = $dataBusinessman;
            $this->view->dataIndicators = $dataBusinessmanIndicators;
            $this->view->SubtotalIzquierdo = $SubtotalIzquierdo;
            $this->view->SubtotalDerecho = $SubtotalDerecho;
            $this->view->totalIzquierdo = $totalIzquierdo;
            $this->view->totalDerecho = $totalDerecho;
        }
        $this->view->dataPatrocinados = $returnPatrocinados;
        $this->view->form = $form;
    }

    public function obtenerSemanasAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $idempresario = $this->getParam('idempresario');
        $rpta = array();
        if (isset($idempresario)) {
            try {
                $billerHelper = $this->getHelper('billerServices');
                $dataPatrocinados = $billerHelper->getPatrocinados(
                        103210, 197, $this->_config['app']['service']['prolife']
                );
                $semanasEmpresario = $dataPatrocinados[$idempresario]['semanasActivas'];
                $rpta = $semanasEmpresario;
            } catch (Exception $e) {
                $rpta['msj'] = $e->getMessage();
            }
        } else {
            $rpta['msj'] = 'faltan datos';
        }
        $this->getResponse()
                ->setHttpResponseCode(200)
                ->setHeader('Content-type', 'application/json; charset=UTF-8', true)
                ->appendBody(json_encode($rpta));
    }

    public function consumoAction($value = '') {
        $this->view->ifram = $this->iframeBusiness($this->_businessman['codsession'], $this->_businessman['codempr']);
    }

    public function afiliacionAction($value = '') {
        $this->view->ifram = $this->iframeBusiness($this->_businessman['codsession'], $this->_businessman['codempr']);
    }

    public function comisionesAction($value = '') {
        $this->view->ifram = $this->iframeBusiness($this->_businessman['codsession'], $this->_businessman['codempr']);
    }

    public function iframeBusiness($codSesion, $codEmpr) {
        return '<form id="ifrmFuxion" action="http://oficina.fuxionbiotech.com/oficina/oficina.htm" target="ifrInicio">
            <input name="codEmpr" type="hidden" value="' . $codEmpr . '">
            <input name="sessionId" type="hidden" value="' . $codSesion . '"> 
            <input name="accion" type="hidden" value="negocio">
            </form>';
    }

    public function selectCalendar($codPais = null) {

        $colPais = array('PER' => 'peru.fxbiotech', 'BOL' => 'bolivia.fuxion'
            , 'CHL' => 'chile.fxbiotech', 'COL' => 'colombia.fxbiotech', 'CRI' => 'costarica.fuxion'
            , 'ECU' => 'ecuador.fuxion', 'MEX' => 'mexico.fxbiotech', 'PAN' => 'panama.fxbiotech'
            , 'USA' => 'usa.fxbiotech', 'VEN' => 'venezuela.fxbiotech');

        $email = isset($colPais[$codPais]) ? $colPais[$codPais] : $colPais['PER'];
        return 'https://www.google.com/calendar/feeds/' . $email . '%40gmail.com/public/basic';
    }

}

