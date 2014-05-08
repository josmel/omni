<?php

class Challenge_Model_Mentor extends Core_Model
{
    protected $_tableMentor; 
    
    public function __construct() {
        $this->_tableMentor = new Application_Model_DbTable_Mentor();
    }    
  
    
    public function findRowByCodemp($codEmp, $visible = false) {
        $select = $this->_tableMentor->getAdapter()->select()
                ->from(array('m' => $this->_tableMentor->getName()), 
                       array('m.idmentor', 'm.codemp', 'm.nommentor', 'apepaterno', 
                           'm.telefono', 'm.correo', 'apematerno', 'm.vchestado'))
                ->where("m.codemp = ?", $codEmp);
        if (!$visible) $select->where("m.vchestado IN ('A', 'I')");
        else $select->where("m.vchestado LIKE 'A'");
      
        $select = $select->query();
        $result = $select->fetch();
        $select->closeCursor();
        
        return $result;
    }
    
    public function findRowByIdMentor($idMentor, $visible = false) {
        $select = $this->_tableMentor->getAdapter()->select()
                ->from(array('m' => $this->_tableMentor->getName()), 
                       array('m.idmentor', 'm.codemp', 'm.nommentor', 'apepaterno', 
                           'm.telefono', 'm.celular', 'm.correo', 'apematerno', 'm.vchestado'))
                ->where("m.idmentor = ?", $idMentor);
        if (!$visible) $select->where("m.vchestado IN ('A', 'I')");
        else $select->where("m.vchestado LIKE 'A'");
        $select = $select->query();
        $result = $select->fetch();
        $select->closeCursor();
        
        return $result;
    }
    
    public function insert($params) {
        $data = array();
          
//        if(isset($params['idparti'])) $data['idparti'] = $params['idparti'];
        if(isset($params['codemp'])) $data['codemp'] = $params['codemp'];
        if(isset($params['nommentor'])) $data['nommentor'] = $params['nommentor'];
        if(isset($params['apepaterno'])) $data['apepaterno'] = $params['apepaterno'];
        if(isset($params['apematerno'])) $data['apematerno'] = $params['apematerno'];
        if(isset($params['telefono'])) $data['telefono'] = $params['telefono'];
        if(isset($params['correo'])) $data['correo'] = $params['correo'];
        if(isset($params['vchestado']))
            $data['vchestado'] = $params['vchestado'] == 1 ? 'A' : 'I';
        if(isset($params['tmsfeccrea'])) $data['tmsfeccrea'] = date('Y-m-d H:i:s');
        if(isset($params['vchusucrea'])) $data['vchusucrea'] = $params['vchusucrea'];
        
        $this->_tableMentor->insert($data);
        return $this->_tableMentor->getAdapter()->lastInsertId();
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
