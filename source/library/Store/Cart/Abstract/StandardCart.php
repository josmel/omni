<?php

class Store_Cart_Abstract_StandardCart extends Store_Cart_Abstract
{
    static private $_instance = null;

    protected $_idOrder = null;
    protected $_idAddress = null;
    protected $_iva = 0;
    protected $_perception = 0;
    protected $_shipPrice = 0;
    protected $_doc = '';
    protected $_ubigeo = '';
    protected $_cardCode = '';
    protected $_transCode = '';
    protected $_payMethod = '';
    protected $_bizpay = null;
    protected $_voucherType = null;
    protected $_dataJoined = null;
    protected $_dataBusinessman = null;
    protected $_currencySimbol = '';
    protected $_currencyDolarFactor = 0;
    protected $_currencyCode = '';
    protected $_discountP = 0;
    protected $_businessName = '';
    protected $_ruc = '';
    protected $_shipType = null;
    protected $_step = 0;
    protected $_totalOrder = 0;
    
    protected $_historyPoints = 0;
    
    protected $_dollarFactor = 1;
    protected $_firstAttempt = true;
    
    public function isFirstAttempt() {
        return $this->_firstAttempt;
    }

    public function setFirstAttempt($firstAttempt) {
        $this->_firstAttempt = $firstAttempt;
    }
    
    public function getHistoryPoins() {
        return $this->_historyPoints;
    }

    public function setHistoryPoins($historyPoints) {
        $this->_historyPoints = $historyPoints;
    }
    
    public function getDollarFactor() {
        return $this->_dollarFactor;
    }

    public function setDollarFactor($dollarFactor) {
        $this->_dollarFactor = $dollarFactor;
    }
    
    public function getCardCode() {
        return $this->_cardCode;
    }

    public function setCardCode($cardCode) {
        $this->_cardCode = $cardCode;
    }
    
    public function getTransCode() {
        return $this->_transCode;
    }

    public function setTransCode($transCode) {
        $this->_transCode = $transCode;
    }
    
    public function getPayMethod() {
        return $this->_payMethod;
    }

    public function setPayMethod($payMethod) {
        $this->_payMethod = $payMethod;
    }

    public function getTotalOrder($type = 'NORMAL') {
        if( $type == 'DOLLAR')
            return $this->_totalOrder/$this->_dollarFactor;
        else
            return $this->_totalOrder;
    }

    public function setTotalOrder($totalOrder) {
        $this->_totalOrder = $totalOrder;
    }
    
    public function getDataJoined() {
        return $this->_dataJoined;
    }

    public function setDataJoined($dataJoined) {
        $this->_dataJoined = $dataJoined;
    }
    
    public function setDataBusinessman($dataBusinessman) {
        $this->_dataBusinessman = $dataBusinessman;
    }

    public function getDataBusinessman() {
        return $this->_dataBusinessman;
    }
    
    public function getIdOrder() {
        return $this->_idOrder;
    }

    public function setIdOrder($idOrder) {
        $this->_idOrder = $idOrder;
    }

    public function getIdAddress() {
        return $this->_idAddress;
    }

    public function setIdAddress($idAddress) {
        $this->_idAddress = $idAddress;
    }

    public function getCurrencySimbol() {
        return $this->_currencySimbol;
    }

    public function setCurrencySimbol($idAddress) {
        $this->_currencySimbol = $idAddress;
    }
    
    public function getCurrencyDolarFactor() {
        return $this->_currencyDolarFactor;
    }

    public function setCurrencyDolarFactor($idAddress) {
        $this->_currencyDolarFactor = $idAddress;
    }
    
    public function getCurrencyCode() {
        return $this->_currencyCode;
    }

    public function setCurrencyCode($idAddress) {
        $this->_currencyCode = $idAddress;
    }
    
    public function getBizpay() {
        return $this->_bizpay;
    }


    public function setBizpay($bizpay) {
        $this->_bizpay = $bizpay;
    }

    public function getIva() {
        return $this->_iva;
    }

    public function setIva($iva) {
        $this->_iva = $iva;
    }
    
    public function getStep() {
        return $this->_step;
    }

    public function setStep($step) {
        $this->_step = $step;
    }
    
    public function getVoucherType() {
        return $this->_voucherType;
    }

    public function setVoucherType($voucherType) {
        $this->_voucherType = $voucherType;
    }
    
    public function getPerception() {
        return $this->_perception;
    }

    public function setPerception($perception) {
        $this->_perception = $perception;
    }

    public function getBusinessName() {
        return $this->_businessName;
    }

    public function setBusinessName($businessName) {
        $this->_businessName = $businessName;
    }
    
     public function getRUC() {
        return $this->_ruc;
    }

    public function setRUC($ruc) {
        $this->_ruc = $ruc;
    }
     public function getDiscountP() {
        return $this->_discountP;
    }

    public function setDiscountP($discountP) {
        $this->_discountP = $discountP;
    }
    
    public function getUbigeo() {
        return $this->_ubigeo;
    }

    public function setUbigeo($ubigeo) {
        $this->_ubigeo = $ubigeo;
    }

    public function getShipPrice() {
        return $this->_shipPrice;
    }

    public function setShipPrice($shipPrice) {
        $this->_shipPrice = $shipPrice;
    }
    
    public function getShipType() {
        return $this->_shipType;
    }

    public function setShipType($shipType) {
        $this->_shipType = $shipType;
    }

    public function getDoc() {
        return $this->_doc;
    }

    public function setDoc($doc) {
        $this->_doc = $doc;
    }

    protected function __construct() {
        parent::__construct();
    }

    static public function getInstance() {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function reset($resetDatabase = false) {
        $this->_contents = new Store_Cart_Item_Collection();
        
        $this->cleanPrivateVars();
        
        $sessionData = Zend_Registry::getInstance()->get(Store_Cart_Factory::SESSION_NAME);
        if (isset($sessionData->cartId)) {
            unset($sessionData->cartId);
        }
    }

    public function addCart(Store_Cart_Item $item) {
        if ($this->inCart($item->getId())) {
            $this->updateQuantity($item->getId(), $item->getQuantity());
        } else {
            $this->_contents->addItem($item->getId(), $item);
            $this->cleanup();
        }
    }

    public function updateQuantity($productId, $quantity, $qtyFromPost = false) {
        $item = $this->findProducto($productId);

        if ($item !== null) {
            $quantity = ($qtyFromPost === true)? $quantity: $item->getQuantity() + $quantity;
            $item->setQuantity($quantity);

            $this->cleanup();
        }
     }

    public function cleanup()
    {
        foreach ($this->_contents->getIterator() as $key => $value) {
            if ($this->getQuantity($key) < 1) {
                $this->_contents->detach($key);
            }
        }

        $this->cleanPrivateVars();
    }

    private function cleanPrivateVars() {
        $this->_idOrder = null;
        $this->_idAddress = null;
        $this->_iva = 0;
        $this->_perception = 0;
        $this->_shipPrice = 0;
        $this->_doc = '';
        $this->_ubigeo = '';
        $this->_cardCode = '';
        $this->_transCode = '';
        $this->_payMethod = '';
        $this->_bizpay = null;
        $this->_voucherType = null;
        $this->_dataJoined = array();
        $this->_dataBusinessman = array();
        $this->_currencySimbol = '';
        $this->_currencyDolarFactor = 0;
        $this->_currencyCode = '';
        $this->_discountP = 0;
        $this->_businessName = '';
        $this->_ruc = '';
        $this->_shipType = null;
        $this->_step = 0;
        $this->_totalOrder = 0;
        $this->_historyPoints = 0;
        $this->_dollarFactor = 1;
        $this->_firstAttempt = true;
    }
    public function countContents() {
        return (int)$this->_contents->count();
    }

    public function getQuantity($productId) {
        if ( $this->inCart($productId) ) {
            if(($item = $this->_contents->getItem($productId)) && ($item->getQuantity()> 0) ){
                return $item->getQuantity();
            }
            return 0;
        } else {
            return 0;
        }
    }

    public function inCart($productId) {
        return $this->_contents->offsetExists($productId);
    }

    public function has($productId) {
        return $this->inCart($productId);
    }

    private function findProducto($productoId) {
        if ($this->inCart($productoId)) {
            return $this->_contents->getItem($productoId);
        }
        return null;
    }

    public function remove($productId) {
        $product = $this->findProducto($productId);
        if ($product !== null) {
            $this->_contents->detach($product);
        }
    }

    public function removeProductos(ArrayAccess $productIds) {
        if ($productIds !== null) {
            for($iterator = $productIds->getIterator();
                $iterator->valid();
                $iterator->next()) {
                $this->remove((String)$iterator->current());
            }
        }
    }

    public function removeAll() {
        $this->reset();
    }

    public function getProducts()
    {
            $this->calculateTotals();
            return $this->_contents;
    }

    public function calculateTotals()
    {
            $this->_total = 0;
            $this->_weight = 0;
            foreach ($this->_contents->getIterator() as $productsId => $item) {
                    $this->_weight += $item->getItemWeight();
                    $this->_total += $item->getImporte();
            }
    }

    public function getContents()
    {
            return $this->_contents;
    }

    public function getTotal()
    {
        $this->calculateTotals();
        return (double)$this->_total;
    }
    
    public function getIdsProduct()
    {
        $ids= [];
        foreach($this->_contents as $key => $value){
            $ids[]=($key);
        }
        return $ids;
    }

    public function getTotalPoints()
    {
        $totalPoints = 0;
        foreach ($this->_contents->getIterator() as $productsId => $item) {
            //var_dump($item);
            if ($item->getProduct()->getEnableDiscount() == '1')
                $totalPoints += $item->getImporte();
        }
        return (double) $totalPoints;
    }

    
    public function getWeight()
    {
        return (double)$this->_weight;
    }

    public function getPoints() {
        $totalPoints = 0;

        foreach ($this->_contents->getIterator() as $productId => $item) {
            if($item->getProduct()->getEnableDiscount() == 1)
                $totalPoints += $item->getItemPoints();
        }
        
        return $totalPoints;
    }
    
    public function getAllPoints() {
        $totalPoints = 0;

        foreach ($this->_contents->getIterator() as $productId => $item) {
            $totalPoints += $item->getItemPoints();
        }
        
        return $totalPoints;
    }

    public function getCartData() {
        $data = array();
        $shipPrice = $this->getShipPrice() * (1 + $this->_iva);
        $data['subTotal'] = number_format($this->getTotal(), 2, '.', ' ');
        $data['iva'] = number_format(($this->getIva() * 100), 2, '.', ' ');
        $data['igvTotal'] = number_format($this->getTotal() * $this->getIva(), 2, '.', ' ');
        $data['shipPrice'] = number_format($shipPrice, 2, '.', ' ');
        $data['total'] = $this->getTotal() + $this->getShipPrice()
                         + ($this->getTotal() * $this->getIva());
        $data['total'] = number_format($data['total'], 2, '.', ' ');

        $totalItems = $this->getTotal() * (1 + $this->_iva);
       
        
        $totalPoints = $this->getTotalPoints() * (1 + $this->_iva);
        $discount = $totalPoints * $this->_discountP;
        
        $totalItemsDiscount = $totalItems - $discount;
        $subTotalOrder = $totalItemsDiscount + $shipPrice;
        $perception = $subTotalOrder * $this->_perception;
        $totalOrder = $subTotalOrder + $perception;
        
        $data['totalItems'] = number_format($totalItems, 2, '.', ' ');
        $data['totalItemsDiscount'] = number_format($totalItemsDiscount, 2, '.', ' ');
        $data['subTotalOrder'] = number_format($subTotalOrder, 2, '.', ' ');
        $data['perception'] = number_format($perception, 2, '.', ' ');
        $data['perceptionP'] = number_format(($this->_perception * 100), 2, '.', ' ');
        $data['discountP'] = number_format(($this->_discountP * 100), 2, '.', ' ');
        $data['discount'] = number_format($discount, 2, '.', ' ');
        
        $data['totalOrder'] = number_format($totalOrder, 2, '.', ' ');
        
        $cartItems = array();
        foreach ($this->_contents->getIterator() as $productsId => $item) {
            $cartItem = array();
            $cartItem['quantity'] = $item->getQuantity();
            $cartItem['price'] = number_format($item->getPrice(), 2, '.', ' ');
            $cartItem['ivaPrice'] = number_format(($item->getPrice() * (1 + $this->_iva)), 2, '.', ' ');
            $cartItem['name'] = $item->getProduct()->getName();
            $cartItem['idProduct'] = $item->getProduct()->getId();
            $cartItem['slug'] = $item->getProduct()->getSlug();
            $cartItem['subTotal'] = number_format($item->getSubtotal(), 2, '.', ' ');
            $cartItem['ivaSubTotal'] = number_format(($item->getSubtotal() * (1 + $this->_iva)), 2, '.', ' ');
            $cartItems[] = $cartItem;
        }

        $data['items'] = $cartItems;

        return $data;
    }
}

