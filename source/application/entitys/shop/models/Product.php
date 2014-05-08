<?php

class Shop_Model_Product extends Core_Model {
    protected $_tableProduct; 
    
    public function __construct() {
        parent::__construct();
        $this->_tableProduct = new Application_Model_DbTable_Product();
    }
    
    private function getBasicSelect($idCountry = null, $filter = "") {
        $select = $this->_tableProduct->getAdapter()->select()
                ->from(array('p' => $this->_tableProduct->getName()), 
                       array('p.codprod', 'p.codtpro', 'p.desprod', 
                             'p.abrprod', 'p.shorttext', 'p.text', 
                             'p.punprod', 'p.slug', 'p.pesoprod', 'p.adeprod',
                             'p.desccaja', 'p.imgextdet', 'p.imgextcat')
                )->where("p.vchestado LIKE 'A'")
                ->where("NOT p.codprod = ?", '131102');
        
        if (trim($filter) != "") {
            $orWhere = $this->_tableProduct->getAdapter()->quoteInto("p.desprod LIKE ?", "%".$filter."%");
            //$orWhere .= " OR ".$this->_tableProduct->getAdapter()->quoteInto("p.shorttext LIKE ?", "%".$filter."%");
            //$orWhere .= " OR ".$this->_tableProduct->getAdapter()->quoteInto("p.text LIKE ?", "%".$filter."%");

            $select->where($orWhere);
        }
        
        if ($idCountry != null) {
            $select = $select->join(array('pre' => 'tprecio'), 
                        " pre.codprod = p.codprod AND pre.vchestado LIKE 'A' ", 
                        array('pre.monprec')
                    )->where('pre.codpais = ?', $idCountry);
        }
        //echo $select;
        return $select;
    }
    
    public function getByType($idType, $idCountry = null, $search = '', $fromCatalog = false, $viewCache = true,
                          $favoriteState = false, $idBusinessman = null, $keyArray = false, $notIds=null) {
        $cache = Zend_Registry::get('Cache');
        $cacheName = 'all_product_'.$idType.'_'.$idCountry.'_'.$search.'_'.($fromCatalog ? 't':'f');                
        if ($viewCache && $result = $cache->load($cacheName)) return $result;
        
        $smt = $this->getBasicSelect($idCountry, $search);
        
        if ($idType != '0') $smt->where('p.codtpro = ?', $idType);
        
        if(!empty($notIds)){
            $smt->where("p.codprod NOT IN (?)", $notIds);
        }
        
        //echo $smt->assemble(); exit;
        $smt = $smt->query();
        
        $vhUrl = new Zend_View_Helper_Url();
        
        $result = array();
        $data = $smt->fetchAll();
        foreach ($data as $row) {
            if(empty($row['slug'])) $row['slug'] = $row['codprod'];
            
            if ($fromCatalog) {
                $row['link'] = $vhUrl->url(array('type' => 'catalog', 'slug' => $row['slug']), 'productDetailType');
            } else {
                $row['link'] = $vhUrl->url(array('slug' => $row['slug']), 'productDetail');
            }
            
            $row['picture'] = 
                $this->_config['app']['imagesProduct'].'catalog/'.$row['codprod'].'.jpg';
            
            if($favoriteState) {
                $row['isfavorite'] = $this->isFavorite($idBusinessman, $row['codprod']);
                
            }
            
            $item = array();
            $item['codprod'] = $row['codprod'];
            $item['title'] = $row['desprod'];
            $item['link'] = $row['link'];
            $item['img'] = $row['picture'];
            $item['category'] = $row['codtpro'];
            
            
            if ($keyArray) $result[] = $row;
            else $result[] = $item;
        }
        //var_dump($result); exit;
        //$result = $smt->fetchAll();
        $smt->closeCursor();
        $cache->save($result, $cacheName, array('product'));

        return $result;
    }
    
    public function getIdsByCategory($idType) {
        $smt = $this->getBasicSelect(null, '');
        $smt = $smt->join(array('tpp' => 'ttipproducto_to_producto'), 
                " tpp.codprod = p.codprod ", 
                array()
            )->where('tpp.codtpro = ?', $idType);
        
        $smt = $smt->query();
        
        $ids = "'-1'";
        while ($row = $smt->fetch()) {
            $ids .= ", '".$row['codprod']."'";
        }
        
        //$result = $smt->fetchAll();
        $smt->closeCursor();
        
        return $ids;
    }
    
    public function getByLine($idType, $idCountry = null, $search = '', $viewCache = true) {
        $cache = Zend_Registry::get('Cache');
        $cacheName = 'all_product_line_'.$idType.'_'.$idCountry.'_'.$search;                
        if ($viewCache && $result = $cache->load($cacheName)) return $result;
        
        $smt = $this->getBasicSelect($idCountry, $search);
        $smt = $smt->join(array('tpp' => 'ttipproducto_to_producto'), 
                " tpp.codprod = p.codprod ", 
                array()
            )->where('tpp.codtpro = ?', $idType);
        $smt->order('p.desprod');
        //echo $smt->assemble(); exit;
        $smt = $smt->query();
        
        $vhUrl = new Zend_View_Helper_Url();
        
        $result = array();
        while ($row = $smt->fetch()) {
            if(empty($row['slug'])) $row['slug'] = $row['codprod'];
            $row['link'] = $vhUrl->url( array('type' => 'line', 'slug' => $row['slug']), 'productDetailType');
            $row['picture'] = 
                $this->_config['app']['imagesProduct'].'catalog/'.$row['codprod'].'.jpg';
            
            $item = array();
            $item['codprod'] = $row['codprod'];
            $item['title'] = $row['desprod'];
            $item['link'] = $row['link'];
            $item['img'] = $row['picture'];
            $item['category'] = $row['codtpro'];
            
            $result[] = $item;
        }
        
        //$result = $smt->fetchAll();
        $smt->closeCursor();
        $cache->save($result, $cacheName, array('product'));
        
        return $result;
    }
    
    public function getByNoLine($idType, $idCountry = null, $search = '', $viewCache = true) {
        $cache = Zend_Registry::get('Cache');
        $cacheName = 'all_product_line_'.$idType.'_'.$idCountry.'_'.$search;                
        if ($viewCache && $result = $cache->load($cacheName)) return $result;
        
        $smt = $this->getBasicSelect($idCountry, $search);
        if($idType != 0) {
            $smt = $smt->join(array('tpp' => 'ttipproducto_to_producto'), 
                    " tpp.codprod = p.codprod ", 
                    array()
                )->where('tpp.codtpro = ?', $idType);
        } else {
            $smt = $smt->join(array('tp' => 'ttipproducto'), 
                    " p.codtpro = tp.codtpro ", 
                    array()
                )->where('tp.catalog = ?', '1');
        }
        $smt->order('p.desprod');
        //echo $smt->assemble(); exit;
        $smt = $smt->query();
        
        $vhUrl = new Zend_View_Helper_Url();
        
        $result = array();
        while ($row = $smt->fetch()) {
            if(empty($row['slug'])) $row['slug'] = $row['codprod']; 
            $row['link'] = $vhUrl->url( array('type' => 'catalog', 'slug' => $row['slug']), 'productDetailType');
            
            $row['picture'] = 
                $this->_config['app']['imagesProduct'].'catalog/'.$row['codprod'].'.jpg';
            
            $item = array();
            $item['codprod'] = $row['codprod'];
            $item['title'] = $row['desprod'];
            $item['link'] = $row['link'];
            $item['img'] = $row['picture'];
            $item['category'] = $row['codtpro'];
            
            $result[] = $item;
        }
        
        //$result = $smt->fetchAll();
        $smt->closeCursor();
        $cache->save($result, $cacheName, array('product'));
        
        return $result;
    }
    
    
    public function getSalients($idCountry) {
        $cache = Zend_Registry::get('Cache');
        $cacheName = 'all_product_'.$idCountry;                
        if ($result = $cache->load($cacheName)) return $result;
        
        
        $limit = isset($this->_config['app']['products']['maxSalients']) ? 
                    $this->_config['app']['products']['maxSalients'] : 0;
        
       
        $smt = $this->getBasicSelect($idCountry);
        $smt->where('p.issalient = ?', '1');
        if ($limit > 0) $smt->limit($limit);
        //echo $smt->assemble(); exit;
        $smt = $smt->query();
        
        $vhUrl = new Zend_View_Helper_Url();
        
        $result = array();
        while ($row = $smt->fetch()) {
            if(empty($row['slug'])) $row['slug'] = $row['codprod'];
            $row['link'] = $vhUrl->url(array('slug' => $row['slug']), 'productDetail');
            $row['picture'] = 
                $this->_config['app']['imagesProduct'].'catalog/'.$row['codprod'].'.jpg';
            
            $item = array();
            $item['code'] = $row['codprod'];
            $item['title'] = $row['desprod'];
            $item['link'] = $row['link'];
            $item['img'] = $row['picture'];
            $item['category'] = $row['codtpro'];
            $item['price'] = number_format($row['monprec'], 2, '.', ' ');
            $result[] = $item;
        }
        
        //$result = $smt->fetchAll();
        $smt->closeCursor();
        $cache->save($result, $cacheName, array('product'));
        
        return $result;
    }
    
    
    public function getByTypePaginated($idType, $idCountry = null, $page = 1, 
                        $itemsPerPage = 1, $search, $order = '1', &$total) {
        $smt = $this->getBasicSelect($idCountry, $search);
        //if ($idType != '0') $smt->where('p.codtpro = ?', $idType);}
        
        if($idType != '0') {
            $smt = $smt->join(array('tpp' => 'ttipproducto_to_producto'), 
                    " tpp.codprod = p.codprod ", 
                    array()
                )->where('tpp.codtpro = ?', $idType);
        } 
        
        switch ($order) {
            case '1': $smt->order('p.desprod ASC'); break; 
            case '2': $smt->order('p.desprod DESC'); break; 
            case '3': $smt->order('pre.monprec DESC'); break; 
            case '4': $smt->order('pre.monprec ASC'); break; 
        }
        
        $inicio = ($page - 1) * $itemsPerPage;
        //$smt->limit($inicio.', '.$itemsPerPage);
        $smt->limitPage($page, $itemsPerPage);
        
        $sqlQuery = trim($smt->assemble());
        $sqlQuery = substr_replace($sqlQuery, 'SELECT SQL_CALC_FOUND_ROWS ', 0, 6);
        //echo $sqlQuery; exit;
        $smt = $this->_tableProduct->getAdapter()->query($sqlQuery);
        $vhUrl = new Zend_View_Helper_Url();
        //$result = $smt->fetchAll();
        $result = array();
        while ($row = $smt->fetch()) {
            if(empty($row['slug'])) $row['slug'] = $row['codprod'];
            $row['link'] = $vhUrl->url(array('slug' => $row['slug']), 'productDetail');
            $row['picture'] = 
                $this->_config['app']['imagesProduct'].'catalog/'.$row['codprod'].'.jpg';
            
            $result[] = $row;
        }
        
        
        $total = $smt->getAdapter()->fetchOne('SELECT FOUND_ROWS()');
        //echo $total; exit;
        $smt->closeCursor();
        
        return $result;
    }
    
    public function getBySlug($slug, $idCountry = null) {
        $smt = $this->getBasicSelect($idCountry);
        $smt->where("p.slug LIKE ?", $slug);
        $smt = $smt->query();
        
        $result = $smt->fetchAll();
        
        if(!empty($result)) $result = $result[0];
        else {
            $smt = $this->getBasicSelect($idCountry);
            $smt->where("p.codprod = ?", $slug);
            $smt = $smt->query();

            $result = $smt->fetchAll();
            if(!empty($result)) { 
                $result = $result[0];
                $this->updateSlug($slug, false);
            }
        }
        
        $smt->closeCursor();
        
        
        return $result;
    }
    
    public function getById($idProduct, $idCountry = null) {
        $smt = $this->getBasicSelect($idCountry);
        if(!empty($idProduct)) $smt->where("p.codprod LIKE ?", $idProduct);
        
        $smt = $smt->query();
        
        $result = $smt->fetchAll();
        $smt->closeCursor();
        
        if(!empty($result)) $result = $result[0];
        return $result;
    }
    
    
    function updateSlugs() {
        $products = $this->_tableProduct->getAll();
        
        foreach ($products as $product) {
            $slug = $this->generateSlug($product['desprod'], 5);
            echo $slug." ---- ".$product['desprod']."<br>";
            $this->_tableProduct->update(
                    array('slug' => $slug), 
                    "codprod LIKE '".$product['codprod']."'"
            );
        }
        
    }
    
    function updateSlug($idProduct, $print = true) {
        $where = $this->_tableProduct->getAdapter()->quoteInto('codprod = ?', $idProduct);
        $products = $this->_tableProduct->getAll($where);
        
        foreach ($products as $product) {
            $slug = $this->generateSlug($product['desprod'], 5);
            if ($print) echo $slug." ---- ".$product['desprod']."<br>";
            $this->_tableProduct->update(
                    array('slug' => $slug), 
                    "codprod LIKE '".$product['codprod']."'"
            );
        }
        
    }
    
    function addFavorite($idBusinessman, $idProduct) {
        $bd = $this->_tableProduct->getAdapter();
        if(!$this->existsFavorite($idBusinessman, $idProduct)) {
            $data = array(
                'codempr' => $idBusinessman,
                'codprod'=> $idProduct,
                'vchestado'=> 'A',
                'vchusucrea'=> $idBusinessman,
                'tmsfeccrea'=> date('Y-m-d H:i:s')
            );

            $bd->insert('tproducto_favorito', $data);
        } else {
            $data = array(
                'vchestado'=> 'A',
                'vchusumodif'=> $idBusinessman,
                'tmsfecmodif'=> date('Y-m-d H:i:s')
            );
            $where = $bd->quoteInto("codempr = ?", $idBusinessman);
            $where .= $bd->quoteInto(" AND codprod = ?", $idProduct);
            $bd->update('tproducto_favorito', $data, $where);
        }
    }
    
    function removeFavorite($idBusinessman, $idProduct) {
        $bd = $this->_tableProduct->getAdapter();
        $where = $bd->quoteInto("codempr = ?", $idBusinessman);
        $where .= $bd->quoteInto(" AND codprod = ?", $idProduct);

        $data = array(
            'vchestado'=> 'D',
            'vchusumodif'=> $idBusinessman,
            'tmsfecmodif'=> date('Y-m-d H:i:s')
        );
        
        $bd->update('tproducto_favorito', $data, $where);
    }
    
    function isFavorite($idBusinessman, $idProduct) {
        $bd = $this->_tableProduct->getAdapter();
        
        $smt = $bd->select()->from('tproducto_favorito')
                ->where("codempr = ?", $idBusinessman)
                ->where("vchestado = ?", 'A')
                ->where("codprod = ?", $idProduct);
        
        $smt = $smt->query();
        $result = $smt->fetchAll();
        $smt->closeCursor();
        
        if(!empty($result)) return true;
        
        return false;
    }
    
    function existsFavorite($idBusinessman, $idProduct) {
        $bd = $this->_tableProduct->getAdapter();
        
        $smt = $bd->select()->from('tproducto_favorito')
                ->where("codempr = ?", $idBusinessman)
                ->where("codprod = ?", $idProduct);
        
        $smt = $smt->query();
        $result = $smt->fetchAll();
        $smt->closeCursor();
        
        if(!empty($result)) return true;
        
        return false;
    }
    
    function getFavorites($idBusinessman, $idCountry, $page = 1, 
                        $itemsPerPage = 1, $order = '1', &$total) {
        $smt = $this->getBasicSelect($idCountry);
        $smt->join(array('pf' => 'tproducto_favorito'), 
                        " pf.codprod = p.codprod AND pf.vchestado LIKE 'A' ")
            ->where("pf.codempr = ?", $idBusinessman)
            ->where("pf.vchestado = ?", 'A');
        
        switch ($order) {
            case '1': $smt->order('p.desprod ASC'); break; 
            case '2': $smt->order('p.desprod DESC'); break; 
            case '3': $smt->order('pre.monprec DESC'); break; 
            case '4': $smt->order('pre.monprec ASC'); break; 
        }
        
        //$inicio = ($page - 1) * $itemsPerPage;
        //$smt->limit($inicio.', '.$itemsPerPage);
        $smt->limitPage($page, $itemsPerPage);
        
        $sqlQuery = trim($smt->assemble());
        $sqlQuery = substr_replace($sqlQuery, 'SELECT SQL_CALC_FOUND_ROWS ', 0, 6);
        //echo $sqlQuery; exit;
        $smt = $this->_tableProduct->getAdapter()->query($sqlQuery);
        
        $vhUrl = new Zend_View_Helper_Url();
        $result = array();
        while ($row = $smt->fetch()) {
            if(empty($row['slug'])) $row['slug'] = $row['codprod'];
            $row['link'] = $vhUrl->url(array('slug' => $row['slug']), 'productDetail');
            $row['picture'] = 
                $this->_config['app']['imagesProduct'].'catalog/'.$row['codprod'].'.jpg';
            $result[] = $row;
        }
        
        $total = $smt->getAdapter()->fetchOne('SELECT FOUND_ROWS()');
        
        $smt->closeCursor();
        
        return $result;
    }
    
    function getAllFavorites($idBusinessman, $idCountry, $notIds=null) {
        $smt = $this->getBasicSelect($idCountry);
        $smt->join(array('pf' => 'tproducto_favorito'), 
                        " pf.codprod = p.codprod AND pf.vchestado LIKE 'A' ")
            ->where("pf.codempr = ?", $idBusinessman)
            ->where("pf.vchestado = ?", 'A');
        if(!empty($notIds)){
            $smt->where("pf.codprod NOT IN (?)", $notIds);
        }

        $smt = $smt->query();
        
        $vhUrl = new Zend_View_Helper_Url();
        $result = array();
        while ($row = $smt->fetch()) {
            if(empty($row['slug'])) $row['slug'] = $row['codprod'];
            $row['link'] = $vhUrl->url(array('slug' => $row['slug']), 'productDetail');
            $row['picture'] = 
                $this->_config['app']['imagesProduct'].'catalog/'.$row['codprod'].'.jpg';
            $row['isfavorite'] = true;
            $result[] = $row;
        }
        
        $smt->closeCursor();
        
        return $result;
    }
    
    function getByOrder($idOrder) {
        $bd = $this->_tableProduct->getAdapter();
        $where = $bd->quoteInto("od.idpedi = ?", $idOrder);
        
        $smt = $this->getBasicSelect();
        $smt->join(array('od' => 'tdetpedido'), 
                        "od.codprod = p.codprod AND ".$where);
        
        $smt = $smt->query();
        $result = $smt->fetchAll();
        $smt->closeCursor();
        
        return $result;
    }
    
    /**
    * Devuelve una coleccion de productos que no presentan los ids ingresados
    *
    * @return array Devuelve una coleccion de productos que no presentan los ids ingresados
    * @param array $collection Coleccion Producto
    * @param array $noIds Array de Ids
    */
    function filterIdsProduct($collection,$noIds){
       $result=[];
       foreach($collection as $prod) {
           if(!in_array($prod["codprod"],$noIds)){
               $result[]=$prod;
           }
       }
       return $result;
    }
}

