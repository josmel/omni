<?php

class Application_Model_DbTable_Businessman extends Core_Db_Table
{
    protected $_name = 'tempresario';
    protected $_primary = "codempr";
    const NAMETABLE = 'tempresario';
    
    /**
     * 
     * @param obj DB $resulQuery
     */
        
    public function getPrimaryKey() {
        return $this->_primary;
    }
    
    public function getWhereActive() {
        return " ";
    }
    
    
       static function populate($params)
    {
        $data = array();
        if(isset($params['subdomain'])) $data['subdomain'] = $params['subdomain'];
        if(isset($params['nomempr'])) $data['nomempr'] = $params['nomempr'];
        $data=  array_filter($data);
        if(isset($params['urlfacebook']))$data['urlfacebook'] = $params['urlfacebook'] ? $params['urlfacebook'] :null;
        if(isset($params['urltwitter'])) $data['urltwitter'] = $params['urltwitter'] ? $params['urltwitter'] :null;
        if(isset($params['urlyoutube'])) $data['urlyoutube'] = $params['urlyoutube'] ? $params['urlyoutube'] : null;
        if(isset($params['urlblog'])) $data['urlblog'] = $params['urlblog'] ? $params['urlblog'] : null;
         if(isset($params['testimony'])) $data['testimony'] = $params['testimony']? $params['testimony'] : null;
        return $data;
    }
 
    public function getBySubDomain($subDomain) {
        $smt = $this->select()
                ->from(array('e' => $this->_name))
                ->where('e.subdomain LIKE ?', $subDomain)->query();
       
        $result = $smt->fetchAll();
        $smt->closeCursor();
        
        if(!empty($result)) { 
            $result = $result[0];
            $contact =  $this->getContactByBusinessman($result['codempr']);
            if(!empty($contact))
                $result = array_merge($result, $contact);
        }
        $smt->closeCursor();
        return $result;
    }
    
    public function getContactByBusinessman($idContact) {
        $smt = $this->getDefaultAdapter()->select()
                ->from('tcontacto', array('phone' => 'telefono', 'cell' => 'celular'))
                ->where("codtcon LIKE 'TC5'")
                ->where("vchestado LIKE 'A'")
                ->where("codempr = ?", $idContact)->query();
       
        $result = $smt->fetchAll();
        $smt->closeCursor();
        if(!empty($result)) 
            $result = $result[0];
            
        return $result;
    }
 
     public function getSubDomain($subDomain) {
        $smt = $this->select()
                ->from(array('e' => $this->_name))
                ->where('e.subdomain LIKE ?', $subDomain)->query();
        $result = $smt->fetch();
        $smt->closeCursor();
        return $result;
    }
}

