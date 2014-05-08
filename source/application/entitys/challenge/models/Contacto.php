<?php

class Challenge_Model_Contacto extends Core_Model
{
    protected $_tableContacto; 
    
    public function __construct() {
        $this->_tableContacto = new Application_Model_DbTable_Contacto();
    }    
  
    
    public function findRowByCodemp($codEmp, $visible = false) {
        $select = $this->_tableContacto->getAdapter()->select()
                ->from(array('p' => $this->_tableContacto->getName()), 
                       array('p.idparti', 'codemp', 'seudonimo', 'p.vchestado'))
                ->where("p.codemp = ?", $codEmp);
        if (!$visible) $select->where("p.vchestado IN ('A', 'I')");
        else $select->where("p.vchestado LIKE 'A'");
      
        $select = $select->query();
        $result = $select->fetch();
        $select->closeCursor();
        
        return $result;
    }
    
    public function insert($params) {
        $data = array();
          
        if(isset($params['nomconta'])) $data['nomconta'] = $params['nomconta'];
        if(isset($params['emailconta'])) $data['emailconta'] = $params['emailconta'];
        if(isset($params['consulta'])) $data['consulta'] = $params['consulta'];
        if(isset($params['vchestado']))
            $data['vchestado'] = $params['vchestado'] == 1 ? 'A' : 'I';
        if(isset($params['tmsfeccrea'])) $data['tmsfeccrea'] = date('Y-m-d H:i:s');
        if(isset($params['vchusucrea'])) $data['vchusucrea'] = $params['vchusucrea'];
        
        $this->_tableContacto->insert($data);
        return $this->_tableContacto->getAdapter()->lastInsertId();
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
