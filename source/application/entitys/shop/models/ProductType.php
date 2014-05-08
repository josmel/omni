<?php

class Shop_Model_ProductType extends Core_Model
{
    protected $_tableProductType; 
    
    public function __construct() {
        parent::__construct();
        $this->_tableProductType = new Application_Model_DbTable_ProductType();
    }
    
    function getAll($getTypeAll = true, $viewCache = true) {
        $result = array();
        $cache = Zend_Registry::get('Cache');
        $cacheName = 'full_product_types_'.($getTypeAll ? 't':'f');                
        if ($viewCache && $result = $cache->load($cacheName)) return $result;

        $smt = $this->_tableProductType->select()
            ->where("vchestado = ?", "A");
        
        $smt = $smt->query();
        
        $result = array();
        $vhUrl = new Zend_View_Helper_Url();
        if($getTypeAll) { 
            $typeAll = array(
                'slug' => 'todos',
                'link' => $vhUrl->url(array('slug' => 'todos'), 'productCategory'),
                'destpro' => 'TODOS',
                'abrtpro' => 'TODOS',
                'hexcolor' => '222222',
                'class' => 'lcatTotal',
                'codtpro' => '0'
            );

            $result[$typeAll['slug']] = $typeAll;
        }
        
        while ($row = $smt->fetch()) {
            if(empty($row['picture'])) {
                $row['picture'] = 
                        $this->_config['app']['defaults']['imageProfile'];
                $row['picture2'] = 
                        $this->_config['app']['defaults']['imageProfile'];
            }

            $row['link'] = $vhUrl->url(array('slug' => $row['slug']), 'productCategory');
            //$row['link'] = 'product/cat/'.$row['slug'];

            $row['picture'] = 
                $this->_config['app']['imagesProductType'].$row['picture'];
            $row['picture2'] = 
                $this->_config['app']['imagesLine'].$row['picture2'];
            $result[$row['slug']] = $row;
        }

        $smt->closeCursor();
          
        $cache->save($result, $cacheName);
        
        return $result;//return $this->_tableProductType->getAll(" vchestado = 'A' and line = '1' ");
    }
    
    function getAllTypes($getTypeAll = true, $flag = 0, $viewCache = true) {
        $result = array();
        $cache = Zend_Registry::get('Cache');
        $cacheName = 'all_product_types_'.($getTypeAll ? 't':'f').'_'.$flag;                
        if ($viewCache && $result = $cache->load($cacheName)) return $result;

        $smt = $this->_tableProductType->select()
            ->where("vchestado = ?", "A");
        
        if ($flag > 0) $smt->where("flagview = ?", $flag);
        else $smt->where("line = ?", '1')->order("flagview DESC");
        $smt = $smt->query();
        
        $result = array();
        $vhUrl = new Zend_View_Helper_Url();
        if($getTypeAll) { 
            $typeAll = array(
                'slug' => 'todos',
                'link' => $vhUrl->url(array('slug' => 'todos'), 'productCategory'),
                'destpro' => 'TODOS',
                'abrtpro' => 'TODOS',
                'hexcolor' => '222222',
                'class' => 'lcatTotal',
                'codtpro' => '0'
            );

            $result[$typeAll['slug']] = $typeAll;
        }
        
        while ($row = $smt->fetch()) {
            if(empty($row['picture'])) {
                $row['picture'] = 
                        $this->_config['app']['defaults']['imageProfile'];
                $row['picture2'] = 
                        $this->_config['app']['defaults']['imageProfile'];
            }

            $row['link'] = $vhUrl->url(array('slug' => $row['slug']), 'productCategory');
            //$row['link'] = 'product/cat/'.$row['slug'];

            $row['picture'] = 
                $this->_config['app']['imagesProductType'].$row['picture'];
            $row['picture2'] = 
                $this->_config['app']['imagesLine'].$row['picture2'];
            $result[$row['slug']] = $row;
        }

        $smt->closeCursor();
          
        $cache->save($result, $cacheName);
        
        return $result;//return $this->_tableProductType->getAll(" vchestado = 'A' and line = '1' ");
    }
    
    function getAllNoTypes($getTypeAll = true, $onlyCatalog = true, $viewCache = true) {
        $result = array();
        $cache = Zend_Registry::get('Cache');
        $cacheName = 'all_product_no_types_'.($getTypeAll ? 't':'f').'_'.($onlyCatalog ? 't':'f');                
        if ($viewCache && $result = $cache->load($cacheName)) return $result;
        
        $smt = $this->_tableProductType->select()
            ->where("vchestado = ?", "A");
        
        if($onlyCatalog) $smt->where("catalog = ?", '1');
        
        $smt = $smt->query();
        
        $result = array();
        $vhUrl = new Zend_View_Helper_Url();
        if($getTypeAll) { 
            $typeAll = array(
                'slug' => 'todos',
                'link' => $vhUrl->url(array('slug' => 'todos'), 'productCategory'),
                'destpro' => 'TODOS',
                'abrtpro' => 'TODOS',
                'picture' => $this->_config['app']['imagesProductType'].$this->_config['app']['defaults']['imageProfile'],
                'picture2' => $this->_config['app']['imagesProductType'].$this->_config['app']['defaults']['imageProfile'],
                'hexcolor' => '444444',
                'class' => 'lcatTotal',
                'codtpro' => '0'
            );

            $result[$typeAll['slug']] = $typeAll;
        }
        
        while ($row = $smt->fetch()) {
            if(empty($row['picture'])) {
                $row['picture'] = 
                        $this->_config['app']['defaults']['imageProfile'];
                $row['picture2'] = 
                        $this->_config['app']['defaults']['imageProfile'];
            }

            $row['link'] = $vhUrl->url(array('slug' => $row['slug']), 'productCategory');
            //$row['link'] = 'product/cat/'.$row['slug'];

            $row['picture'] = 
                $this->_config['app']['imagesProductType'].$row['picture'];
            $row['picture2'] = 
                $this->_config['app']['imagesLine'].$row['picture2'];
            
            $result[$row['slug']] = $row;
        }

        $smt->closeCursor();
          
        $cache->save($result, $cacheName);
        
        return $result;//return $this->_tableProductType->getAll(" vchestado = 'A' and catalog = '1' ");
    }
    
    function updateSlugs() {
        $types = $this->_tableProductType->getAll();
        
        foreach ($types as $type) {
            $slug = $this->generateSlug($type['destpro']);
            echo $slug." ---- ".$type['destpro']."<br>";
            $this->_tableProductType->update(array('slug' => $slug), "codtpro like '".$type['codtpro']."'");
        }
    }
    
    function removeProduct($idCategory, $idProduct) {
        $where = $this->_tableProductType->getAdapter()->quoteInto('codtpro LIKE ?', $idCategory);
        $where .= " ".$this->_tableProductType->getAdapter()->quoteInto(' AND codprod = ?', $idProduct);
        //var_dump($where);
        
        $cache = Zend_Registry::get('Cache');
        $cache->clean(Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG, array('product'));
        
        return $this->_tableProductType->getAdapter()->delete('ttipproducto_to_producto', $where);
    }
    
    function addProduct($idCategory, $idProduct) {
        $data = array('codprod' => $idProduct, 'codtpro' => $idCategory);
        //var_dump($data);
        $cache = Zend_Registry::get('Cache');
        $cache->clean(Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG, array('product'));
        
        return $this->_tableProductType->getAdapter()->insert('ttipproducto_to_producto', $data);
    }
}

