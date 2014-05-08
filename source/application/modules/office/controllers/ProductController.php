<?php

class Office_ProductController extends Core_Controller_ActionOffice {
    public function init() {
        parent::init();
    }

    public function indexAction() {
       
        $mProductType = new Shop_Model_ProductType();
        $mProduct = new Shop_Model_Product();
        
        $productLines = $mProductType->getAllTypes(false);
        //$productSupraLines = $mProductType->getAllTypes(false, 2);
        $productCategories = $mProductType->getAllNoTypes(false);
        
        $typeAll = array(
            'slug' => 'todos',
            'link' => $this->view->url(array('slug' => 'todos'), 'productCategory'),
            'destpro' => 'TODOS',
            'hexcolor' => '444444',
            'class' => 'lcatTotal',
            'codtpro' => '0'
        );
        
        $page = $this->getParam('page', 1);
        $search = $this->getParam('search', '');
        $order = $this->getParam('order', '1');

        $itemsPerPage = $this->_config['app']['paginator']['nItemsProducts'];
        $selectedType = $this->getParam('slug', 'productos-clave'); //todos
        
        if (isset($productLines[$selectedType])) {
            $selectedType = $productLines[$selectedType];
            $selectedType['type'] = 'line';
        } /* elseif (isset($productSupraLines[$selectedType])) {
            $selectedType = $productSupraLines[$selectedType];
            $selectedType['type'] = 'supraline';
        } */ elseif (isset($productCategories[$selectedType])) {
            $selectedType = $productCategories[$selectedType];
            $selectedType['type'] = 'category';
        } else {
            $selectedType = $typeAll;
            $selectedType['type'] = 'all';
        }
        $initParams = array('cat' => $selectedType['slug'], 'order' => $order);
        if(!empty($search)) $initParams['search'] = $search;
        $initParams['page'] = $page;
        
        $urlBase = "/product";
        $totalCount = 0;
        //var_dump($selectedType);
        $products = $mProduct->getByTypePaginated($selectedType['codtpro'], $this->_businessman['codpais'], 
                $page, $itemsPerPage, $search, $order, $totalCount);
        //var_dump($productTypes);
        //var_dump($products);
        //exit;
        $iva = $this->_businessman['iva'];
        
        $nItemBegin = 1 + (($page - 1) * $itemsPerPage);
        $this->view->nItemBegin = $nItemBegin > $totalCount ? 0 : $nItemBegin;
        $nItemEnd = $nItemBegin + count($products) - 1;
        $this->view->nItemEnd = $nItemEnd > $totalCount ? 0 : $nItemEnd;
        $this->view->nTotal = $totalCount;
        $this->view->initParams = $initParams;
        $this->view->urlBase = $urlBase;
        $this->view->products = $products;
        $this->view->productLines = $productLines;
        //$this->view->productSupraLines = $productSupraLines;
        $this->view->productCategories = $productCategories;
        $this->view->selectedType = $selectedType;
        $this->view->iva = $iva;
        
        $totalPages = number_format($totalCount/$itemsPerPage, 0, '.', '');
        if (($totalPages * $itemsPerPage) < $totalCount) $totalPages++;
        $this->view->totalPages = $totalPages;
        $this->view->page = $page;
        $this->view->order = $order;
        $this->addYosonVar('urlOrderProducts', $this->view->urlNav(SITE_URL.'product', $initParams, array('order' => '__ORDER__', 'page' => 1)));
        $this->addYosonVar('monSimbol', $this->_businessman['simbolo']);
        //$this->addYosonVar('dataProduct', Zend_Json_Encoder::encode($products), false);
        
        $breadcums = array(
            'product' => array('label' => 'Tienda', 'url' => '')
        );

        $this->view->breadcums = $breadcums;
    }
    
    public function detailAction() {
        $modelProductType = new Shop_Model_ProductType();
        $modelProduct = new Shop_Model_Product();
        $mDiscount = new Shop_Model_Discount();
        
        $productTypes = $modelProductType->getAllTypes();
        
        $product = $modelProduct->getBySlug(
                trim($this->getParam('slug', '')), 
                $this->_businessman['codpais']
            );
        
        if(!empty($product)) {
            //$this->addYosonVar('idProduct',$product['codprod']);
            //$this->addYosonVar('urlCart','/cart/ajax-add-product/');
            $product['link'] = $this->view->url(
                    array('slug' => $product['slug']),
                    'productDetail'
                );
            $product['picture'] = $this->_config['app']['imagesProduct']
                .'origin-detail/'.$product['codprod'].'.jpg';
        } else {
            $msg = "La ruta no esta relacionada a ningún producto.";
            $this->view->msg = $msg;
        }
        
        $dataCart = Store_Cart_Factory::createInstance();
        $points = $dataCart->getPoints();
        $discounts = array();
        if ($product['punprod'] > 0) {
            $discounts = $mDiscount->findAllByBusinessmanType($this->_businessman['codtemp']); 
            $discountP = 0;        
            foreach ($discounts as $dis) {
                if ($dis['pindesc'] <= $points && $points <= $dis['pfidesc']) 
                    $discountP = $dis['pordesc'];
            }
        }
        
        $iva = $this->_businessman['iva'];
        $finalPrice = $product['monprec']*(1.0+$iva);
       
        $product['finalPrice'] = number_format($finalPrice, 2, '.', '');
        
        //var_dump($product);
        //var_dump($discounts);
        $isFavorite = $modelProduct->isFavorite($this->_businessman['codempr'], $product['codprod']);
        
        $this->view->product = $product;
        $this->view->types = $productTypes;
        $this->view->discounts = $discounts;
        $this->view->isFavorite = $isFavorite;
        //var_dump($product); exit;
        $this->addYosonVar('monSimbol', $this->_businessman['simbolo']);

        $breadcums = array(
            'product' => array('label' => 'Tienda', 'url' =>'/product'),
            'detail' => array('label' => $product['desprod'], 'url' => '')
        );
        
        $this->view->breadcums = $breadcums;
    }
    
    public function pendingPaymentsAction() {
        $mOrder = new Shop_Model_Order();
        $orders = $mOrder->findPendantsByBusinessman($this->_businessman['codempr']);
        
        $this->view->orders = $orders;
        
        $breadcums = array(
            'product' => array('label' => 'Tienda', 'url' =>'/product'),
            'pending-pay' => array('label' => 'Pagos Pendientes', 'url' => '')
        );
        $this->view->breadcums = $breadcums;
    }
    
    public function pendingPaymentDetailAction() {
        $idOrder = $this->_getParam('id', 0);
        
        $mOrder = new Shop_Model_Order();
        $order = $mOrder->findById($idOrder);
        $mProduct = new Shop_Model_Product();
        $products = $mProduct->getByOrder($idOrder);
        
        
        $mAddress = new Businessman_Model_ShipAddress();
        $address = $mAddress->findByIdOrderExtend($idOrder);
        
        if(empty($address)) $address = 'SHOP'; 
       
        $mDiscount = new Shop_Model_Discount();
        $discounts = $mDiscount->findAllByBusinessmanType($this->_businessman['codtemp']); 
        
        var_dump($order); 
        var_dump($products); 
        var_dump($address); //return;
        
        $this->view->address = $address;
        $this->view->products = $products;
        //var_dump($address); 
        
        $discountP = 50.0;//$dataCart->getDiscountP();
        $iva = 0.18;//$this->_businessman['iva'];
        $subTotal = 1000;// $dataCart->getTotal();
        $igv = $subTotal * $iva;
        $total = $subTotal + $igv;
        $totalPoints = 150;//$dataCart->getTotalPoints();
        $discount = $totalPoints * $discountP * (1 + $iva);
        $totalItems = $total - $discount;
        $perceptionP = 0.02;// $dataCart->getPerception();
        $shipPrice = 5; //$dataCart->getShipPrice();
        $subTotalOrder = $totalItems + $shipPrice;
        $perception = $subTotalOrder * $perceptionP;
        $totalOrder = $subTotalOrder + $perception;
        
        
        $this->view->subTotal = number_format($subTotal, 2, '.', ' ');
        $this->view->igvTotal = number_format($igv, 2, '.', ' ');
        $this->view->total = number_format($total, 2, '.', ' ');
        $this->view->discount = number_format($discount, 2, '.', ' ');
        $this->view->discountP = number_format($discountP * 100, 2, '.', ' ');
        $this->view->totalPoints = $totalPoints;
        $this->view->totalOrder = number_format($totalOrder, 2, '.', ' ');
        $this->view->perception = number_format($perception, 2, '.', ' ');
        $this->view->perceptionP = number_format(($perceptionP * 100), 2, '.', ' ');
        $this->view->totalItems = number_format($totalItems, 2, '.', ' ');
        $this->view->subTotalOrder = number_format($subTotalOrder, 2, '.', ' ');
        
        $this->view->iva = $iva;
        $this->view->shipPrice = number_format($shipPrice, 2, '.', '');    
        
        $this->view->businessName = "AAA";//$dataCart->getBusinessName();
        //$this->view->vType = $vType;
        
        
        $breadcums = array(
            'product' => array('label' => 'Tienda', 'url' =>'/product'),
            'pending-pay' => array('label' => 'Pagos Pendientes', 'url' => '/product/pending-payments'),
            'pending-pay-detail' => array('label' => 'Detalle Pago Pediente', 'url' => '')
        );
        $this->view->breadcums = $breadcums;
    }
    
    public function myPurchasesAction() {
        $mBuyDocument = new Biller_Model_BuyDocument();
        $buys = $mBuyDocument->findByBusinessman($this->_businessman['codempr']);
        
        var_dump($buys);
        
        $this->view->buys = $buys;
    }
    
    public function purchaseDetailAction() {
        $idPurchase = $this->_getParam('id', 0);
        $mBuyDocument = new Biller_Model_BuyDocument();
        $buy = $mBuyDocument->getById($idPurchase);
        $details = $mBuyDocument->getDetails($idPurchase);
        
        var_dump($buy);
        var_dump($details);
        
        $this->view->buy = $buy;
        $this->view->details = $details;
    }
    
    public function ajaxAddFavoriteAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true); 
        
        $idProduct = $this->_getParam('idproduct', null);

        $state = 0;
        $msg = "";
        
        try {
            $mProduct = new Shop_Model_Product();
            $mProduct->addFavorite($this->_businessman['codempr'], $idProduct);

            $msg = "Se agregó correctamente el producto a los favoritos.";
            $state = 1;
        } catch (Exception $ex) {
            $msg = "Error de Conexión.";    
        }
        
        $return = array(
            'state' => $state, 
            'msg' => $msg
        );
        
        $this->getResponse()            
            ->setHttpResponseCode(200)
            ->setHeader('Content-type', 'application/json; charset=UTF-8', true)
            ->appendBody(json_encode($return));
        
    }
    
    public function ajaxRemoveFavoriteAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true); 
        
        $idProduct = $this->_getParam('idproduct', null);
        
        $state = 0;
        $msg = "";
        
        try {
            $mProduct = new Shop_Model_Product();
            $mProduct->removeFavorite($this->_businessman['codempr'], $idProduct);

            $msg = "Se quitó correctamente el producto de los favoritos.";
            $state = 1;
        } catch (Exception $ex) {
            $msg = "Error de Conexión.";    
        }
        
        $return = array(
            'state' => $state, 
            'msg' => $msg
        );
        
        $this->getResponse()            
            ->setHttpResponseCode(200)
            ->setHeader('Content-type', 'application/json; charset=UTF-8', true)
            ->appendBody(json_encode($return));
    }
    
    public function favoritesAction() {
        $mProduct = new Shop_Model_Product();
        
        $page = $this->getParam('page', 1);
        $order = $this->getParam('order', '1');
        $itemsPerPage = $this->_config['app']['paginator']['nItemsProducts'];
        
        $initParams = array('order' => $order);
        $initParams['page'] = $page;
        
        $totalCount = 0;
        
        $favorites = $mProduct->getFavorites($this->_businessman['codempr'], $this->_businessman['codpais'], $page, $itemsPerPage, $order, $totalCount);
        $this->view->favorites = $favorites;
        //var_dump($favorites); exit;
        
        $nItemBegin = 1 + (($page - 1) * $itemsPerPage);
        $this->view->nItemBegin = $nItemBegin > $totalCount ? 0 : $nItemBegin;
        $nItemEnd = $nItemBegin + count($favorites) - 1;
        $this->view->nItemEnd = $nItemEnd > $totalCount ? 0 : $nItemEnd;
        $this->view->nTotal = $totalCount;
        
        $urlBase = "/favorites";
        $urlBaseProduct = "/product";
        $this->view->initParams = $initParams;
        $this->view->urlBase = $urlBase;
        $this->view->urlBaseProduct = $urlBaseProduct;
        
        $totalPages = number_format($totalCount/$itemsPerPage, 0, '.', '');
        if (($totalPages * $itemsPerPage) < $totalCount) $totalPages++;
        $this->view->totalPages = $totalPages;
        $this->view->page = $page;
        $this->view->order = $order;
        $this->view->iva = $this->_businessman['iva'];
        
        $this->addYosonVar('urlOrderProducts', $this->view->urlNav(SITE_URL.'favorites', $initParams, array('order' => '__ORDER__', 'page' => 1)));
        $this->addYosonVar('monSimbol', $this->_businessman['simbolo']);
        
        $mProductType = new Shop_Model_ProductType();
        
        $productLines = $mProductType->getAllTypes(false);
        $productCategories = $mProductType->getAllNoTypes(false);
        $this->view->productLines = $productLines;
        $this->view->productCategories = $productCategories;
        $this->addYosonVar('monSimbol', $this->_businessman['simbolo']);
        
        $breadcums = array(
            'product' => array('label' => 'Tienda', 'url' => '')
        );

        $this->view->breadcums = $breadcums;
    }
}

