<?php

class Challenge_Model_Ciclo extends Core_Model
{
    protected $_tableCiclo; 
    
    public function __construct() {
        $this->_tableCiclo = new Application_Model_DbTable_Ciclo();
    }    
  
    
    public function findRowByIdciclo($IdCiclo, $visible = false) {
        $select = $this->_tableCiclo->getAdapter()->select()
                ->from(array('c' => $this->_tableCiclo->getName()), 
                       array('c.idciclo', 'c.nomciclo', 'c.vchestado'))
                ->where("c.idciclo = ?", $IdCiclo);
        if (!$visible) $select->where("c.vchestado IN ('A', 'I')");
        else $select->where("c.vchestado LIKE 'A'");
      
        $select = $select->query();
        $result = $select->fetch();
        $select->closeCursor();
        
        return $result;
    }
    
    public function findRowByEstado($estado) {
        $select = $this->_tableCiclo->getAdapter()->select()
                ->from(array('c' => $this->_tableCiclo->getName()))
                ->where("c.vchestado LIKE ?", $estado);
      
        $select = $select->query();
        $result = $select->fetch();
        $select->closeCursor();
        
        return $result;
    }
   
    public function getByEstado($estado) {
        $select = $this->_tableCiclo->getAdapter()->select()
                ->from(array('c' => $this->_tableCiclo->getName()))
                ->where("c.vchestado LIKE ?", $estado);
      
        $select = $select->query();
        $result = $select->fetchAll();
        $select->closeCursor();
        
        return $result;
    }
    
    public function findById($id) {
        $select = $this->_tableCiclo->getAdapter()->select()
                ->from(array('c' => $this->_tableCiclo->getName()))
                ->where("c.idciclo = ?", $id);
      
        $select = $select->query();
        $result = $select->fetch();
        $select->closeCursor();
        
        return $result;
    }
    
    public function findCicloActivo() {
        $select = $this->_tableCiclo->getAdapter()->select()
                ->from(array('c' => $this->_tableCiclo->getName()))
                ->where("c.vchestado LIKE ?", 'A')
                ->where("c.habilitado = ?", 1);
      
        $select = $select->query();
        $result = $select->fetch();
        $select->closeCursor();
        
        return $result;
    }
    
    public function insert($params) {
        $data = array();
          
        if(isset($params['idimagen'])) $data['idimagen'] = $params['idimagen'];
        if(isset($params['codtbanner'])) $data['codtbanner'] = $params['codtbanner'];
        if(isset($params['titulo'])) $data['titulo'] = $params['titulo'];
        if(isset($params['descripcion'])) $data['descripcion'] = $params['descripcion'];
        if(isset($params['norder'])) $data['norder'] = $params['norder'];
        if(isset($params['url'])) $data['url'] = $params['url'];
        if(isset($params['vchestado']))
            $data['vchestado'] = $params['vchestado'] == 1 ? 'A' : 'I';
        if(isset($params['tmsfeccrea'])) date('Y-m-d H:i:s');
        if(isset($params['vchusucrea'])) $data['vchusucrea'] = $params['vchusucrea'];
        
        $this->_tableBanner->insert($data);
        return $this->_tableBanner->getAdapter()->lastInsertId();
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
