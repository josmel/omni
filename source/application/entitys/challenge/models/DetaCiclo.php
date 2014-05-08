<?php

class Challenge_Model_DetaCiclo extends Core_Model
{
    protected $_tableDetaCiclo; 
    
    public function __construct() {
        $this->_tableDetaCiclo = new Application_Model_DbTable_DetaCiclo();
    }    
  
    public function findbyId($idCycleDetail) {
        $select = $this->_tableDetaCiclo->getAdapter()->select()
                ->from(array('dc' => $this->_tableDetaCiclo->getName()), 
                       array('dc.talla', 'dc.muneca', 'dc.peso', 'dc.indgrasa', 
                           'dc.espalda', 'dc.cintura', 'dc.cadera', 'dc.pecho',
                           'dc.fotowincha', 'dc.fotofrente',
                           'dc.fotoperfil', 'fotootros', 'dc.codfactcompra',
                           'dc.cantcompra', 'dc.vchestado', 'dc.tmsfeccrea', 'dc.iddetacic'))
                ->where("dc.iddetacic = ?", $idCycleDetail);
      
        $select = $select->query();
        $result = $select->fetchAll();
        $select->closeCursor();
        
        if(!empty($result)) $result = $result[0];
        
        return $result;
    }
    
    public function findAllByIdCicParti($idcicparti, $visible = false){
        $select = $this->_tableDetaCiclo->getAdapter()->select()
                ->from(array('dc' => $this->_tableDetaCiclo->getName()), 
                       array('dc.talla', 'dc.muneca', 'dc.peso', 'dc.indgrasa', 
                           'dc.espalda', 'dc.cintura', 'dc.cadera', 'dc.pecho',
                           'dc.fotowincha', 'dc.fotofrente',
                           'dc.fotoperfil', 'fotootros', 'dc.codfactcompra',
                           'dc.cantcompra', 'dc.vchestado', 'dc.tmsfeccrea', 'dc.iddetacic'))
                ->where("dc.idcicparti = ?", $idcicparti)
                ->order("dc.tmsfeccrea ASC");
        if (!$visible) $select->where("dc.vchestado IN ('A', 'I')");
        else $select->where("dc.vchestado LIKE 'A'");
      
        $select = $select->query();
        $result = $select->fetchAll();
        $select->closeCursor();
        
        return $result;
    }
    
    public function insert($params) {
        $data = array();
          
        if(isset($params['idcicparti'])) $data['idcicparti'] = $params['idcicparti'];
        if(isset($params['idtipmusc'])) $data['idtipmusc'] = $params['idtipmusc'];
        if(isset($params['deporte'])) $data['deporte'] = $params['deporte'];
        if(isset($params['talla'])) $data['talla'] = $params['talla'];
        if(isset($params['muneca'])) $data['muneca'] = $params['muneca'];
        if(isset($params['peso'])) $data['peso'] = $params['peso'];
        if(isset($params['indgrasa'])) $data['indgrasa'] = $params['indgrasa'];
        if(isset($params['pecho'])) $data['pecho'] = $params['pecho'];
        if(isset($params['espalda'])) $data['espalda'] = $params['espalda'];
        if(isset($params['cintura'])) $data['cintura'] = $params['cintura'];
        if(isset($params['cadera'])) $data['cadera'] = $params['cadera'];
        if(isset($params['fotowincha'])) $data['fotowincha'] = $params['fotowincha'];
        if(isset($params['fotofrente'])) $data['fotofrente'] = $params['fotofrente'];
        if(isset($params['fotoperfil'])) $data['fotoperfil'] = $params['fotoperfil'];
        if(isset($params['fotootros'])) $data['fotootros'] = $params['fotootros'];
        if(isset($params['codfactcompra'])) $data['codfactcompra'] = $params['codfactcompra'];
        if(isset($params['cantcompra'])) $data['cantcompra'] = $params['cantcompra'];
        if(isset($params['fecini'])) $data['fecini'] = $params['fecini'];
        if(isset($params['fecfin'])) $data['fecfin'] = $params['fecfin'];
        if(isset($params['semana'])) $data['semana'] = $params['semana'];
        if(isset($params['vchestado']))
            $data['vchestado'] = $params['vchestado'] == 1 ? 'A' : 'I';
        if(isset($params['tmsfeccrea'])) $data['tmsfeccrea'] = date('Y-m-d H:i:s');
        if(isset($params['vchusucrea'])) $data['vchusucrea'] = $params['vchusucrea'];
        if(isset($params['cuello'])) $data['cuello'] = $params['cuello'];
        $this->_tableDetaCiclo->insert($data);
        return $this->_tableDetaCiclo->getAdapter()->lastInsertId();
    }
    
    public function getRowMeasure($idcicparti, $inifin = 'ini', $visible = false){
        $select = $this->_tableDetaCiclo->getAdapter()->select()
                ->from(array('dc' => $this->_tableDetaCiclo->getName()), 
                       array('dc.talla', 'dc.muneca', 'dc.peso', 'dc.indgrasa', 
                           'dc.espalda', 'dc.cintura', 'dc.cadera', 'dc.pecho',
                           'dc.fotowincha', 'dc.fotofrente',
                           'dc.fotoperfil', 'fotootros', 'dc.codfactcompra',
                           'dc.cantcompra', 'dc.vchestado', 'dc.fecini', 'dc.fecfin', 'dc.idtipmusc'))
                ->where("dc.idcicparti = ?", $idcicparti)
                ->limit(1);
        if($inifin == 'ini') $select->order('dc.iddetacic asc');
        else $select->order('dc.iddetacic desc');
        
        if (!$visible) $select->where("dc.vchestado IN ('A', 'I')");
        else $select->where("dc.vchestado LIKE 'A'");
      
        $select = $select->query();
        $result = $select->fetch();
        $select->closeCursor();
        
        return $result;
    }
    
    public function validateInsert($fecha, $idcicparti, $visible = false) {
        $select = $this->_tableDetaCiclo->getAdapter()->select()
                ->from(array('dc' => $this->_tableDetaCiclo->getName()), 
                       array('dc.talla', 'dc.muneca', 'dc.peso', 'dc.indgrasa', 
                           'dc.espalda', 'dc.cintura', 'dc.cadera', 'dc.pecho',
                           'dc.fotowincha', 'dc.fotofrente',
                           'dc.fotoperfil', 'fotootros', 'dc.codfactcompra',
                           'dc.cantcompra', 'dc.vchestado'))
                ->where("dc.idcicparti = ?", $idcicparti)
                ->where("dc.fecini <= ?", $fecha)
                ->where("dc.fecfin >= ?", $fecha)
                ;
        if (!$visible) $select->where("dc.vchestado IN ('A', 'I')");
        else $select->where("dc.vchestado LIKE 'A'");
        $select = $select->query();
        $result = $select->fetch();
        $select->closeCursor();
        
        return $result;
    }
    
    public function verifyDataInscription(/*$fecha, */$visible = false)
    {
        $select = $this->_tableDetaCiclo->getAdapter()->select()
                ->from(array('dc' => $this->_tableDetaCiclo->getName()), 
                       array('dc.talla', 'dc.muneca', 'dc.peso', 'dc.indgrasa', 
                           'dc.espalda', 'dc.cintura', 'dc.cadera', 'dc.pecho',
                           'dc.fotowincha', 'dc.fotofrente', 'dc.cuello',
                           'dc.fotoperfil', 'fotootros', 'dc.codfactcompra',
                           'dc.cantcompra', 'dc.vchestado', 'dc.idcicparti',
                           'dc.fecini', 'dc.fecfin', 'dc.semana'))
                ->where("dc.semana = ?", 0)
//                ->where("dc.fecini <= ?", $fecha)
//                ->where("dc.fecfin >= ?", $fecha)
                ;
        if (!$visible) $select->where("dc.vchestado IN ('A', 'I')");
        else $select->where("dc.vchestado LIKE 'A'");
        $select = $select->query();
        $result = $select->fetchAll();
        $select->closeCursor();
        return $result;
    }
    
}
