<?php
class QueryTable_ParticipantesDetalle {
                
    private $_sqlQuery = "SELECT SQL_CALC_FOUND_ROWS p.idparti, p.nombre, p.apellidos, p.codemp, p.estadoofi, 
                                        p.seudonimo, cp.idciclo, cp.idmentor, cp.kilobaja, p.sexo, 
                                        cp.fecini, cp.fecfin, cp.nrosemana, dcl.semana semactual,
                                        dcl.fecini fechainicio, dcl.fecfin fechafin, 
                                        dcf.talla,
                                        dca.cintura cinturault, dca.cadera caderault, dca.peso pesoult  
                        FROM tparticipantes as p 
                        INNER JOIN tcicloparticipantes as cp on cp.idparti = p.idparti AND cp.idciclo = __CYCLE__  
                        INNER JOIN tdetaciclo as dcl on dcl.idcicparti = cp.idcicparti and dcl.semana = (select max(dc.semana) from tdetaciclo as dc where dc.idcicparti = cp.idcicparti group by dc.idcicparti) 
                        INNER JOIN tdetaciclo as dcf on dcf.idcicparti = cp.idcicparti and dcf.semana = 0 
                         INNER JOIN tdetaciclo as dca on dca.idcicparti = cp.idcicparti AND dca.semana = __WEEK__                         
                        __SEARCH__ ";
    private $_sqlQuerySearch = "";
    private $_viewColumns = array('check', 'nombre', 'kilos', 'imc', 'cintura', 'cadera', 'estadoofi', 'estadodesa');
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

        for ($i=0; $i < count($this->_viewColumns); $i++) {
            $column = $this->_viewColumns[$i];
            switch ($column) {
                case 'check' : 
                    $rowResult[$i] = '<input type="checkbox" class="chkAdmin" value="'.$row['idparti'].'" />';
                    break;
                case 'nombre': 
                    $rowResult[$i] = $row['nombre'].' '.$row['apellidos'];
                    break;
                case 'kilos': 
                    $rowResult[$i] = $row['pesoult'];
                    break; 
                case 'imc': 
                    $dataImc = $bStats->imcData($row['pesoult'], ($row['talla']/100), $row['sexo']);
                    $rowResult[$i] = number_format($dataImc['imc'], 2, ".", "");
                    //$rowResult[$i] = $imcini.' - '.$imcult;
                    break; 
                case 'cintura': 
                    $rowResult[$i] = $row['cinturault'];
                    break; 
                case 'cadera': 
                    $rowResult[$i] = $row['caderault'];
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
