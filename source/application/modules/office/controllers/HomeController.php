<?php

class Office_HomeController extends Core_Controller_ActionOffice {

    public function init() {
        /* Initialize action controller here */
        parent::init();
    }

    public function indexAction() {

        $mBanner = new Admin_Model_Banner();
        $mVideo = new Admin_Model_Video();
        $mBlog = new Admin_Model_Blog();

        $objType = new Businessman_Model_Report();
        $codsema = $objType->getAffiliate($this->_businessman['codempr']);
        $idsemana = $codsema['codsema'];
        $semanas = $objType->getWeekOne($idsemana);        
        $picture = DINAMIC_URL . "businessman/" . $this->_config['app']['businessman']['picture']['two']
                ['height'] . 'x' . $this->_config['app']['businessman']['picture']['two']['width'] . '/' . $this->_businessman['picture'];
        $billerHelper = $this->getHelper('billerServices');
        $dataBusinessman = $billerHelper->getDataBusinessman(
                $this->_businessman['codempr'], $semanas['codsema'], $this->_config['app']['service']['prolife']
        );
        $dataBusinessmanRange = $billerHelper->getBusinessmanRange(
                $this->_businessman['codempr'], $semanas['codsema'], $this->_config['app']['service']['prolife']
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
        $this->view->dataBusinessmanRange = $dataBusinessmanRange;
        $this->view->data = $dataBusinessman;
        $this->view->picture = $picture;
        $this->view->SubtotalIzquierdo = $SubtotalIzquierdo;
        $this->view->SubtotalDerecho = $SubtotalDerecho;
        $this->view->totalIzquierdo = $totalIzquierdo;
        $this->view->totalDerecho = $totalDerecho;
        $this->view->bannersHome = $mBanner->findAllByType(
                $this->_config['app']['banner']['home'], true, 
                array(
                    'CODSESSION' => $this->_businessman['codsession']
                    ));
        $this->view->bannersHallFame = $mBanner->findAllByType(
                $this->_config['app']['banner']['hallFame'], true);
        $this->view->video = $mVideo->findAllByType(
                $this->_config['app']['video']['home'], true);
        $this->view->blogs = $mBlog->findAll();
        $this->view->iframe = $this->iframeHome($this->_businessman['codsession'], $this->_businessman['codempr']);
        $this->addYosonVar('urlCalendar', $this->selectCalendar(
                        $this->_businessman['sigpais']));
    }

    public function iframeHome($codSesion, $codEmpr) {
        return '<form id="ifrmFuxion" action="http://oficina.fuxionbiotech.com/oficina/oficina.htm" target="ifrInicio">
            <input name="codEmpr" type="hidden" value="' . $codEmpr . '">
            <input name="sessionId" type="hidden" value="' . $codSesion . '">
            <input name="accion" type="hidden" value="inicio">
            </form>';
    }

    // colombia.fxbiotech%40
    public function selectCalendar($codPais = null) {
        $colPais = array('PER' => 'peru.fxbiotech', 'BOL' => 'bolivia.fuxion'
            , 'CHL' => 'chile.fxbiotech', 'COL' => 'colombia.fxbiotech', 'CRI' => 'costarica.fuxion'
            , 'ECU' => 'ecuador.fuxion', 'MEX' => 'mexico.fxbiotech', 'PAN' => 'panama.fxbiotech'
            , 'USA' => 'usa.fxbiotech', 'VEN' => 'venezuela.fxbiotech');

        $email = isset($colPais[$codPais]) ? $colPais[$codPais] : $colPais['PER'];
        return 'https://www.google.com/calendar/feeds/' . $email . '%40gmail.com/public/basic';
    }

}

