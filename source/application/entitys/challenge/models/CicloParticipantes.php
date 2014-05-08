<?php

class Challenge_Model_CicloParticipantes extends Core_Model
{
    protected $_tableCicloParticipantes; 
    
    public function __construct() {
        $this->_tableCicloParticipantes = new Application_Model_DbTable_CicloParticipantes();
    }    
  
    
    public function findRowByIdCicloPart($idCicloPart, $visible = false) {
        $select = $this->_tableCicloParticipantes->getAdapter()->select()
                ->from(array('cp' => $this->_tableCicloParticipantes->getName()), 
                       array('cp.idcicparti', 'cp.idmentor', 'cp.tiembaja', 'cp.kilobaja', 'cp.indgrasa', 
                           'cp.espalda', 'cp.cintura', 'cp.cadera', 'cp.motivo',
                           'cp.compromiso', 'cp.vchestado', 'cp.idparti'))              
                ->where("cp.idcicparti = ?", $idCicloPart);
        if (!$visible) $select->where("cp.vchestado IN ('A', 'I')");
        else $select->where("cp.vchestado LIKE 'A'");
      
        $select = $select->query();
        $result = $select->fetch();
        $select->closeCursor();
        
        return $result;
    }
    /**
     * Retorna los datos de inicio del participante
     * @param int $codPart codigo de participante
     */
    public function dataFatPart($codPart)
    {
        
        $select = $this->_tableCicloParticipantes->getAdapter()->select()
            ->from(array('cp' => $this->_tableCicloParticipantes->getName()), 
                   array('cp.idcicparti', 'cp.idmentor', 'cp.tiembaja', 'cp.kilobaja', 'cp.indgrasa', 
                      'cp.motivo','cp.compromiso', 'cp.vchestado', 'cp.idparti'))
            ->join(array('dc'=>'tdetaciclo'),"cp.idcicparti = dc.idcicparti", 
                   array('dc.cadera','dc.cuello','dc.cintura','dc.talla',
                       'dc.peso','dc.idtipmusc')
                )
            ->where("cp.vchestado = 'A' ")
            ->where("cp.vchestado LIKE 'A'")
            ->where("idparti = ? ",$codPart)
            ->order("dc.iddetacic ASC")
            ->limit(1)
            ->query();       
            $result = $select->fetch();
            $select->closeCursor();        
            return $result;
    }
        
    public function findRowByIdPartiIdciclo($codEmp, $idCiclo, $visible = false) {
        $select = $this->_tableCicloParticipantes->getAdapter()->select()
                ->from(array('cp' => $this->_tableCicloParticipantes->getName()), 
                       array('cp.idcicparti', 'cp.idmentor', 'cp.tiembaja', 'cp.kilobaja', 'cp.indgrasa', 
                           'cp.espalda', 'cp.cintura', 'cp.cadera', 'cp.motivo',
                           'cp.compromiso', 'cp.vchestado'))
                ->join(array('c' => 'tciclo'), 
                        "c.idciclo = cp.idciclo", 
                        array('c.idciclo', 'c.nomciclo', 'c.fecinicio', 'c.fecfin',
                            'c.vchestado'))
                ->join(array('p' => 'tparticipantes'), 
                        "p.idparti = p.idparti", 
                        array('p.codemp', 'p.vchestado'))
                ->where("cp.idparti = ?", $codEmp)
                ->where("cp.idciclo = ?", $idCiclo);
        if (!$visible) $select->where("cp.vchestado IN ('A', 'I')");
        else $select->where("cp.vchestado LIKE 'A'");
      
        $select = $select->query();
        $result = $select->fetch();
        $select->closeCursor();
        
        return $result;
    }
    
    public function getByDateRange($idCompetitor, $beginDate, $endDate, $visible = false) {
        $begin = $beginDate->get(Zend_Date::YEAR.'-'.Zend_Date::MONTH.'-'.Zend_Date::DAY);  
        $end = $endDate->get(Zend_Date::YEAR.'-'.Zend_Date::MONTH.'-'.Zend_Date::DAY);  
        
        $bd = $this->_tableCicloParticipantes->getAdapter();
        $where = $bd->quoteInto("(cp.fecfin >= ?", $begin)." AND ".
                 $bd->quoteInto("cp.fecfin <= ?)", $end)." OR ".
                 $bd->quoteInto("(cp.fecini >= ?", $begin)." AND ".
                 $bd->quoteInto("cp.fecini <= ?)", $end)." OR ".
                 $bd->quoteInto("(cp.fecini <= ?", $begin)." AND ".
                 $bd->quoteInto("cp.fecfin >= ?)", $begin)." OR ".
                 $bd->quoteInto("(cp.fecini <= ?", $end)." AND ".
                 $bd->quoteInto("cp.fecfin >= ?)", $end);
        
        $select = $this->_tableCicloParticipantes->getAdapter()->select()
                ->from(array('cp' => $this->_tableCicloParticipantes->getName()), 
                       array('cp.idcicparti', 'pfInicio' => 'cp.fecini', 'pfFin' => 'cp.fecfin'))
                ->join(array('c' => 'tciclo'), 
                        "c.idciclo = cp.idciclo", 
                        array('c.idciclo', 'c.nomciclo', 'c.fecinicio', 'c.fecfin',
                            'c.vchestado'))
                ->where("cp.idparti = ?", $idCompetitor)
                ->where($where);
        if (!$visible) $select->where("cp.vchestado IN ('A', 'I')");
        else $select->where("cp.vchestado LIKE 'A'");
      
        //echo $select->assemble(); exit;
        $select = $select->query();
        $result = $select->fetchAll();
        $select->closeCursor();
        
        return $result;
    }
    
    public function getActiveCycleByComepetitor($idCompetitor, $visible = false) {
        $today = date('Y-m-d');
        
        $bd = $this->_tableCicloParticipantes->getAdapter();
        $where = $bd->quoteInto("(cp.fecfin >= ?", $today.' 0:00:00')." AND ".
                 $bd->quoteInto("cp.fecini <= ?)", $today.' 23:59:59');
        
        $select = $bd->select()
                ->from(array('cp' => $this->_tableCicloParticipantes->getName()), array())
                ->join(array('c' => 'tciclo'), 
                        "c.idciclo = cp.idciclo")
                ->where("cp.idparti = ?", $idCompetitor)
                ->where($where);
        
        if (!$visible) $select->where("cp.vchestado IN ('A', 'I')");
        else $select->where("cp.vchestado LIKE 'A'");
      
        //echo $select->assemble(); exit;
        $select = $select->query();
        $result = $select->fetch();
        $select->closeCursor();
        
        return $result;
    }
    
    public function getWarningStateCompetitors($visible = false) {
        $sqlQuery = "SELECT p.idparti, p.nombre, p.apellidos, p.codemp, p.estadoofi, p.email, 
                        p.seudonimo, p.sexo, 
                        cp.fecini, cp.fecfin, cp.nrosemana, dcl.semana semactual, 
                        dcl.fecini fechainicio, dcl.fecfin fechafin, DATEDIFF(CURDATE(), dcl.fecfin) diaspasados 
                    FROM tparticipantes as p  
                    INNER JOIN tcicloparticipantes as cp on cp.idparti = p.idparti
                    INNER JOIN tdetaciclo as dcl on dcl.idcicparti = cp.idcicparti and dcl.semana = (select max(dc.semana) from tdetaciclo as dc where dc.idcicparti = cp.idcicparti group by dc.idcicparti) 
                    WHERE DATEDIFF(CURDATE(), dcl.fecfin) >= 6 AND DATEDIFF(CURDATE(), dcl.fecfin) <= 7 ";
        
        $bd = $this->_tableCicloParticipantes->getAdapter();
        

        if (!$visible) $sqlQuery.=" AND cp.vchestado IN ('A', 'I')";
        else $sqlQuery.=" AND cp.vchestado LIKE 'A'";
      
        //echo $select->assemble(); exit;
        $select = $bd->query($sqlQuery);
        $result = $select->fetchAll();
        $select->closeCursor();
        
        return $result;
    }
    
    public function insert($params) {
        $data = array();
          
        if(isset($params['idciclo'])) $data['idciclo'] = $params['idciclo'];
        if(isset($params['idparti'])) $data['idparti'] = $params['idparti'];
        if(isset($params['idmentor'])) $data['idmentor'] = $params['idmentor'];
        if(isset($params['tiembaja'])) $data['tiembaja'] = $params['tiembaja'];
        if(isset($params['kilobaja'])) $data['kilobaja'] = $params['kilobaja'];
        if(isset($params['indgrasa'])) $data['indgrasa'] = $params['indgrasa'];
        if(isset($params['espalda'])) $data['espalda'] = $params['espalda'];
        if(isset($params['cintura'])) $data['cintura'] = $params['cintura'];
        if(isset($params['cadera'])) $data['cadera'] = $params['cadera'];
        if(isset($params['motivo'])) $data['motivo'] = $params['motivo'];
        if(isset($params['compromiso'])) $data['compromiso'] = $params['compromiso'];
        if(isset($params['fecini'])) $data['fecini'] = $params['fecini'];
        if(isset($params['fecfin'])) $data['fecfin'] = $params['fecfin'];
        if(isset($params['nrosemana'])) $data['nrosemana'] = $params['nrosemana'];
        if(isset($params['vchestado']))
            $data['vchestado'] = $params['vchestado'] == 1 ? 'A' : 'I';
        if(isset($params['tmsfeccrea'])) $data['tmsfeccrea'] = date('Y-m-d H:i:s');
        if(isset($params['vchusucrea'])) $data['vchusucrea'] = $params['vchusucrea'];       
        $this->_tableCicloParticipantes->insert($data);
        return $this->_tableCicloParticipantes->getAdapter()->lastInsertId();
    }
    
    public function update($params, $idBanner) {
        $data = array();
          
        if(isset($params['idimagen'])) $data['idimagen'] = $params['idimagen'];
        if(isset($params['codtbanner'])) $data['codtbanner'] = $params['codtbanner'];
        if(isset($params['titulo'])) $data['titulo'] = $params['titulo'];
        if(isset($params['descripcion'])) $data['descripcion'] = $params['descripcion'];
        if(isset($params['norder'])) $data['norder'] = $params['norder'];
        if(isset($params['url'])) $data['url'] = $params['url'];
        if(isset($params['vchestado'])) 
            $data['vchestado'] = $params['vchestado'] == 1 ? 'A' : 'I';
        if(isset($params['tmsfecmodif'])) date('Y-m-d H:i:s');
        if(isset($params['vchusumodif'])) $data['vchusumodif'] = $params['vchusumodif'];
        
        $where = $this->_tableBanner->getAdapter()->quoteInto('idbanner = ?', $idBanner);
        $this->_tableBanner->update($data, $where);
        return $this->_tableBanner->getAdapter()->lastInsertId();
    }
    
    public function deleteAll($notDelete = "", $idBannerType = "") {
        $where = "";
        if(!empty($notDelete))
            $where .= $this->_tableBanner->getAdapter()
                          ->quoteInto('NOT idbanner IN (?)', explode(',', $notDelete));
        if(!empty($idBannerType))
            $where .= (!empty ($where) ? ' AND ' : '').
                      $this->_tableBanner->getAdapter()
                          ->quoteInto('codtbanner LIKE ?', $idBannerType);
        
        $this->_tableBanner->delete($where);
    }
    
}
