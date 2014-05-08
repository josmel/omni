<?php

class QueryTable_ParticipantesAvance {
                
    private $_sqlQuery = "SELECT SQL_CALC_FOUND_ROWS p.idparti, p.nombre, p.apellidos, p.codemp, p.estadoofi, 
                                        p.seudonimo, cp.idciclo, cp.idmentor, cp.kilobaja, p.sexo, 
                                        cp.fecini, cp.fecfin, cp.nrosemana, dcl.semana semactual,
                                        dcl.fecini fechainicio, dcl.fecfin fechafin, 
                                        dca.cintura cinturault, dca.cadera caderault, dca.peso pesoult, 
                                    dcf.cintura cinturaini, dcf.cadera caderaini, dcf.talla, dcf.peso pesoini 
                        FROM tparticipantes as p 
                        INNER JOIN tcicloparticipantes as cp on cp.idparti = p.idparti AND cp.idciclo = __CYCLE__  
                        INNER JOIN tdetaciclo as dcl on dcl.idcicparti = cp.idcicparti AND dcl.semana = (select max(dc.semana) from tdetaciclo as dc where dc.idcicparti = cp.idcicparti group by dc.idcicparti) 
                        INNER JOIN tdetaciclo as dca on dca.idcicparti = cp.idcicparti AND dca.semana = __WEEK__ 
                        INNER JOIN tdetaciclo as dcf on dcf.idcicparti = cp.idcicparti AND dcf.semana = 0 
                        __SEARCH__ ";
    private $_sqlQuerySearch = "";
    private $_viewColumns = array('check', 'nombre', 'kilosdif', 'kilosper', 
        'imcdif', 'imcper', 'cinturadif', 'cinturaper', 'caderadif', 'caderaper', 
        'estadoofi', 'estadodesa', 'action');
    private $_primary = 'idparti';
    
    /**
     *
     * @var Zend_Db_Adapter_Abstract
     */
    private $_adapter = null;
    
    public function __construct($cycle = 0, $week = 0) {
        $this->_sqlQuery = str_replace("__CYCLE__", $cycle, $this->_sqlQuery);
        $this->_sqlQuery = str_replace("__WEEK__", $week, $this->_sqlQuery);
    }  
    
    public function getSqlQuery() {
        return str_replace("__SEARCH__", $this->_sqlQuerySearch, $this->_sqlQuery);
    }
    
    public function getPrimary() {
        return $this->_primary;
    }
    
    public function setSearch($search) {
        if (!empty($search)) {
            $adapter = $this->getAdapter();
            $this->_sqlQuerySearch = $adapter->quoteInto(" WHERE CONCAT(p.nombre, ' ', p.apellidos) LIKE ? ", "%".$search."%");
        } else { 
            $this->_sqlQuerySearch = "";
        }
    }
    
    public function getAdapter() {
        if ($this->_adapter == null) 
            $this->_adapter = Zend_Registry::get('dbChallenge');
        
        return $this->_adapter;
    }
    
    public function decorator($row) {
        $rowResult = array();
        $bStats = new Core_Utils_BodyStats();

        $dataImcIni = $bStats->imcData($row['pesoini'], ($row['talla']), $row['sexo']);
        $dataImcUlt = $bStats->imcData($row['pesoult'], ($row['talla']), $row['sexo']);
        
        $imcini = $dataImcIni['imc'];
        $imcult = $dataImcUlt['imc'];
        
        for ($i=0; $i < count($this->_viewColumns); $i++) {
            $column = $this->_viewColumns[$i];
            switch ($column) {
                case 'check' : 
                    $rowResult[$i] = '<input type="checkbox" class="chkAdmin" value="'.$row['idparti'].'" />';
                    break;
                case 'action' : 
                    $rowResult[$i] = '<a href="/challenge/detail-progress/?idciclo='.$row['idciclo'].'&codemp='.$row['codemp'].'">Detalle<a/>';
                    break;
                case 'nombre': 
                    $rowResult[$i] = $row['nombre'].' '.$row['apellidos'];
                    break; 
                case 'progress': 
                    $rowResult[$i] = "10.0 %";
                    break; 
                case 'kilosdif': 
                    $rowResult[$i] = $row['pesoini'] - $row['pesoult'];
                    break; 
                case 'kilosper': 
                    $per = (($row['pesoini'] - $row['pesoult'])/$row['pesoini']) * 100;
                    $rowResult[$i] = number_format($per, 2, ".", "");
                    break; 
                case 'imcdif': 
                    $imc = $imcini - $imcult;
                    $rowResult[$i] = number_format($imc, 2, ".", "");
                    //$rowResult[$i] = $imcini.' - '.$imcult;
                    break; 
                case 'imcper': 
                    $per = (($imcini - $imcult)/$imcini) * 100;
                    $rowResult[$i] = number_format($per, 2, ".", "");
                    break; 
                case 'cinturadif': 
                    $rowResult[$i] = $row['cinturaini'] - $row['cinturault'];
                    break; 
                case 'cinturaper': 
                    $per = (($row['cinturaini'] - $row['cinturault'])/$row['cinturaini']) * 100;
                    $rowResult[$i] = number_format($per, 2, ".", "");
                    break; 
                case 'caderadif': 
                    $rowResult[$i] = $row['caderaini'] - $row['caderault'];
                    break; 
                case 'caderaper': 
                    $per = (($row['caderaini'] - $row['caderault'])/$row['caderaini']) * 100;
                    $rowResult[$i] = number_format($per, 2, ".", "");
                    break; 
                case 'estadoofi': 
                    $rowResult[$i] = $row['estadoofi'] == 1 ? "SI" : "NO";
                    break; 
                case 'estadodesa': 
                    $lastDate = new Zend_Date(substr($row['fechafin'], 0, 10), Zend_Date::YEAR.'-'.Zend_Date::MONTH.'-'.Zend_Date::DAY);
                    $lastDate->addDay(7);
                    $todayDate = new Zend_Date(date('Y-m-d'), Zend_Date::YEAR.'-'.Zend_Date::MONTH.'-'.Zend_Date::DAY);
                    $dayTime = 86400;
                    $difTime = $lastDate->getTimestamp() - $todayDate->getTimestamp();
                    
                    if($difTime < 0) {
                        $rowResult[$i] = "NO";
                    } elseif ($difTime < ($dayTime * 2)) {
                        $rowResult[$i] = "MASO";
                    } else {
                        $rowResult[$i] = "SI";
                    }
                    break; 
            }
        }
        
        return $rowResult;
    }    
}
