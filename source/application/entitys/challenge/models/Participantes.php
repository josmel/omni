<?php

class Challenge_Model_Participantes extends Core_Model
{
    protected $_tableParticipantes; 
    
    public function __construct() {
        $this->_tableParticipantes = new Application_Model_DbTable_Participantes();
    }    
  
    
    public function findRowByIdParti($idParti, $visible = false) {
        $select = $this->_tableParticipantes->getAdapter()->select()
                ->from(array('p' => $this->_tableParticipantes->getName()), 
                       array('p.idparti', 'codemp', 'seudonimo', 'p.vchestado'
                           ,'p.nombre', 'p.apellidos', 'p.estadoofi'))
                ->where("p.idparti = ?", $idParti);
        if (!$visible) $select->where("p.vchestado IN ('A', 'I')");
        else $select->where("p.vchestado LIKE 'A'");
      
        $select = $select->query();
        $result = $select->fetch();
        $select->closeCursor();
        
        return $result;
    }
    
    public function findRowByCodemp($codEmp, $visible = false) {
        $select = $this->_tableParticipantes->getAdapter()->select()
                ->from(array('p' => $this->_tableParticipantes->getName()), 
                       array('p.idparti', 'codemp', 'seudonimo', 'p.vchestado'
                           ,'p.nombre', 'p.apellidos'))
                ->where("p.codemp = ?", $codEmp);
        if (!$visible) $select->where("p.vchestado IN ('A', 'I')");
        else $select->where("p.vchestado LIKE 'A'");
      
        $select = $select->query();
        $result = $select->fetch();
        $select->closeCursor();
        
        return $result;
    }
    
    public function getByIds($ids, $visible = false) {
        $select = $this->_tableParticipantes->getAdapter()->select()
                ->from(array('p' => $this->_tableParticipantes->getName()))
                ->where("p.idparti IN (?)", $ids);
        
        if (!$visible) $select->where("p.vchestado IN ('A', 'I')");
        else $select->where("p.vchestado LIKE 'A'");
      
        $select = $select->query();
        $result = $select->fetchAll();
        $select->closeCursor();
        
        return $result;
    }
    
    
    public function insert($params) {
        $data = array();
//        if(isset($params['idparti'])) $data['idparti'] = $params['idparti'];
        if(isset($params['codemp'])) $data['codemp'] = $params['codemp'];
        if(isset($params['sexo'])) $data['sexo'] = $params['sexo'];
        if(isset($params['nombre'])) $data['nombre'] = $params['nombre'];
        if(isset($params['apellidos'])) $data['apellidos'] = $params['apellidos'];
        if(isset($params['estadoofi'])) $data['estadoofi'] = $params['estadoofi'];
        if(isset($params['seudonimo'])) $data['seudonimo'] = $params['seudonimo'];
        if(isset($params['edad'])) $data['edad'] = $params['edad'];
        if(isset($params['vchestado']))
            $data['vchestado'] = $params['vchestado'] == 1 ? 'A' : 'I';
        if(isset($params['tmsfeccrea'])) $data['tmsfeccrea'] = date('Y-m-d H:i:s');
        if(isset($params['vchusucrea'])) $data['vchusucrea'] = $params['vchusucrea'];
        
        $this->_tableParticipantes->insert($data);
        return $this->_tableParticipantes->getAdapter()->lastInsertId();
    }
    
    public function update($params, $id) {
        $data = array();
          
        if(isset($params['codemp'])) $data['codemp'] = $params['codemp'];
        if(isset($params['sexo'])) $data['sexo'] = $params['sexo'];
        if(isset($params['nombre'])) $data['nombre'] = $params['nombre'];
        if(isset($params['apellidos'])) $data['apellidos'] = $params['apellidos'];
        if(isset($params['estadoofi'])) $data['estadoofi'] = $params['estadoofi'];
        if(isset($params['seudonimo'])) $data['seudonimo'] = $params['seudonimo'];
        if(isset($params['edad'])) $data['edad'] = $params['edad'];
        
        if(isset($params['vchestado'])) 
            $data['vchestado'] = $params['vchestado'] == 1 ? 'A' : 'I';
        if(isset($params['tmsfecmodif'])) date('Y-m-d H:i:s');
        if(isset($params['vchusumodif'])) $data['vchusumodif'] = $params['vchusumodif'];
        
        $where = $this->_tableParticipantes->getAdapter()->quoteInto('idparti = ?', $id);
        return $this->_tableParticipantes->update($data, $where);        
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
    
    public function reactivate($ids, $params) {
        $data = array();
          
        if(isset($params['estadoofi'])) $data['estadoofi'] = $params['estadoofi'];
        
        if(isset($params['vchestado'])) 
            $data['vchestado'] = $params['vchestado'] == 1 ? 'A' : 'I';
        if(isset($params['tmsfecmodif'])) 
            $data['vchusumodif'] = isset($params['vchusumodif'])?$params['vchusumodif']:date('Y-m-d H:i:s');
        if(isset($params['vchusumodif'])) $data['vchusumodif'] = $params['vchusumodif'];
        
        $where = $this->_tableParticipantes->getAdapter()->quoteInto('idparti in (?)', $ids);
        return $this->_tableParticipantes->update($data, $where);  
    }
    
}
