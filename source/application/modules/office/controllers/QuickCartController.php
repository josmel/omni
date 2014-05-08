 <?php

class Office_QuickCartController extends Core_Controller_ActionOffice
{

    public function init() {
        parent::init();
    }

    public function indexAction() {
        $dataCart = Store_Cart_Factory::createInstance();
        $dataCart->setStep(1);        

        //Niveles de ubigeo
        $countryLevels = array();
        $yosonLevelNames = array();
        for ($i = 1 ; $i <= $this->_businessman['nivpais']; $i++) {
            $countryLevels[] = $this->_businessman['nomniv'.$i];
            $yosonLevelNames[] = "-".$this->_businessman['nomniv'.$i]."-";
        }
        
        $this->view->levelNames = $countryLevels;
        $this->addYosonVar('levelNames', Zend_Json_Encoder::encode($yosonLevelNames), false);
        
        //Ubigeo del primer nivel
        $mUbigeo = new Businessman_Model_Ubigeo();
        $levelOneOptions = $mUbigeo->findAllByCountryPairs($this->_businessman['codpais'], "");
        $this->view->levelOneOptions = $levelOneOptions;
        
        //Todas las Categorias
        $mCategory = new Shop_Model_ProductType();
        $categories = $mCategory->getAll(true);
        $this->view->categories = $categories;
        
        //Nombre de Codigo Postal para USA
        $zipCodeName = "Código Postal";
        if ($this->_businessman['codpais'] == '840') $zipCodeName = "Zip Code";
        $this->view->zipCodeName = $zipCodeName;
        
        //Iva por defecto
        $iva = $this->_businessman['iva'];
        
        //Precio de envio y direcciones 
        $mShipPrice = new Shop_Model_ShipPrice();
        $mAddress = new Businessman_Model_Address();
        $addresses = $mAddress->findAllByBusinessman($this->_businessman['codempr'], $this->_businessman['codpais']); 
        $shipPrice = 0;
        $yosonAddress = array();
        $selAddress = $dataCart->getIdAddress();
        foreach ($addresses as $item) {
            if (empty($selAddress)) $selAddress = $item['iddire'];
            $largeDesDire = (!empty($item['destvia'])) ? 
                                $item['abbrtvia'].' '.$item['desdire'].' '.$item['numdire'].' Int. '.$item['intdire']
                                : $item['desdire'];
            
            $largeDesDire .= " - ".$item['ubigeoName']." ";
            if (!empty($item['refdire'])) $largeDesDire .= "(".$item['refdire'].") ";
            $largeDesDire .= "/ Teléfono: ".$item['telefono']." / Contacto: ".$item['nomcont'];
            
            $addresses[$item['iddire']]['largeDesAddress'] = $largeDesDire;
            $shipPriceObj = $mShipPrice->getByUbigeo($item['codubig'], $item['codpais']);
            $itemShipPrice = isset($shipPriceObj['monflet']) ? $shipPriceObj['monflet'] : 0;
            $itemIva = $item['iva'] == null ? $this->_businessman['iva'] : $item['iva'];
            $shipPriceIGV= $itemShipPrice * (1 + $itemIva);
            $yosonAddress[$item['iddire']] = array('iva' => $itemIva, 'shipPrice' => $shipPriceIGV);
            
            if($item['iddire'] == $selAddress) {
                $shipPrice = $itemShipPrice * (1 + $itemIva);
                $dataCart->setIva($itemIva);
                $dataCart->setShipPrice($shipPrice);
            }
        }
        $yosonAddress["-1"]= array('iva' => $this->_businessman['iva'], 'shipPrice' => "0");
        $this->addYosonVar('addressesData', Zend_Json_Encoder::encode($yosonAddress), false);
        $this->view->addresses = $addresses;
        $this->view->selAddress = $selAddress;
        
        //Tipos de Comprobante 
        $mVType = new Shop_Model_VoucherType();
        $vTypes = $mVType->findAll();
        
        $perceptionData = null;
        if(isset($this->_config['app']['perception'][$this->_businessman['sigpais']]))
            $perceptionData = $this->_config['app']['perception'][$this->_businessman['sigpais']];
        $this->addYosonVar('perception', Zend_Json_Encoder::encode($perceptionData), false);
        $this->view->perceptionData = $perceptionData;
        $this->view->vTypes = $vTypes;
        
        $this->view->ruc = $this->_businessman['rucempr'];
        $this->view->businessName = $this->_businessman['nomempr'].' '.$this->_businessman['appempr'].' '.$this->_businessman['apmempr'];
        $this->view->isFac = false;
        
        $vType = $dataCart->getVoucherType();
        
        if (empty($vType) && count($vTypes) > 0) $vType = $vTypes[0]['codtdov'];
        $selVType = array();
        foreach ($vTypes as $vt) {
            if ($vt['codtdov'] == $vType) $selVType = $vt;
        } 
        $this->view->selVType = $selVType;
        $perception = $dataCart->getPerception();
        
        //Metodos de pago
        $formPayment = new Shop_Form_Payment(
            $this->_businessman, 0, 0, 0, 0,
            number_format($dataCart->getTotal(), 2, '.', ' '), 
            false
        );
        $formPayment->addElement('hash', 'tokenHash', array('salt' => 'Solicitud no válida.'));
        $formPayment->addDecoratorCustom('forms/_formPaymentQC.phtml');
        $this->view->formPayment = $formPayment;
        
        //Productos favoritos
        $mProduct = new Shop_Model_Product();
        $idsCart= $dataCart->getIdsProduct();
        $favorites = $mProduct->getAllFavorites($this->_businessman['codempr'], $this->_businessman['codpais']);
        $prodSearch= $mProduct->filterIdsProduct($favorites, $idsCart);
        $this->view->products = $prodSearch;
        
        $this->addYosonVar('products', Zend_Json_Encoder::encode($favorites), false);
        
        //Descuentos
        $minPoints = $this->_config['app']['pointsToDiscount'];
        $historyPoints = (int) $this->_businessman['historyPoints'];
        $mDiscount = new Shop_Model_Discount();
        $points = $dataCart->getPoints();
        //Descuentos por tipo de cliente. Búsqueda del descuento correspondiente
        $discounts = $mDiscount->findAllByBusinessmanType($this->_businessman['codtemp']); //
        $discountP = -1;       
        $pointsToNextDiscount = 0;       
        $nextDiscount = 0;       
        $discountsYoson = array();
        foreach ($discounts as $dis) {
            if($discountP != -1 && $pointsToNextDiscount == 0){
                $nextDiscount = $dis['pordesc'];
                $pointsToNextDiscount = $dis['pindesc'] - $points;
            }
            if ($dis['pindesc'] <= $points && $points <= $dis['pfidesc']) 
                $discountP = $dis['pordesc'];
            $dYoson = array(
                'discount' => $dis['pordesc'],
                'pStart' => $dis['pindesc'],
                'pEnd' => $dis['pfidesc']
            );
            $discountsYoson[] = $dYoson;
        }
        
        $hasDiscount= true;
        
        if (($points + $historyPoints) < $minPoints) {
            $hasDiscount = false;
            $pointsToNextDiscount = $minPoints - ($points + $historyPoints);
            $discountP = 0;
        }

        if($discountP == -1) $discountP = 0;
        
        $this->addYosonVar('discounts', Zend_Json_Encoder::encode($discountsYoson), false);
        $this->addYosonVar('currency', $this->_businessman["simbolo"]);
        $this->addYosonVar('ubigeoLevels', $this->_businessman['nivpais'], false);
        $this->addYosonVar('iva', $iva);
        $this->addYosonVar('minPoints', $minPoints);
        $this->addYosonVar('historyPoints', $historyPoints);
        
        if($discountP == -1) $discountP = 0;
        
        //Calculo de montos de la orden
        $discountP = $dataCart->getDiscountP();
        $subTotal = $dataCart->getTotal();
        $igv = $subTotal * $iva;
        $total = $subTotal + $igv;
        $totalPoints = $dataCart->getTotalPoints();
        $discount = $totalPoints * $discountP * (1 + $iva);
        $totalItems = $total - $discount;
        $perceptionP = $dataCart->getPerception();
        $shipPrice = $dataCart->getShipPrice();
        $subTotalOrder = $totalItems + $shipPrice;
        $perception = $subTotalOrder * $perceptionP;
        $totalOrder = $subTotalOrder + $perception;
        
        $this->view->subTotal = number_format($subTotal, 2, '.', ' ');
        $this->view->igvTotal = number_format($igv, 2, '.', ' ');
        $this->view->total = number_format($total, 2, '.', ' ');
        $this->view->discount = number_format($discount, 2, '.', ' ');
        $this->view->discountP = number_format($discountP * 100, 2, '.', ' ');
        $this->view->totalWeight = $dataCart->getWeight();
        $this->view->totalPoints = $totalPoints;
        $this->view->totalOrder = number_format($totalOrder, 2, '.', ' ');
        $this->view->perception = number_format($perception, 2, '.', ' ');
        $this->view->perceptionP = number_format(($perceptionP * 100), 2, '.', ' ');
        $this->view->totalItems = number_format($totalItems, 2, '.', ' ');
        $this->view->subTotalOrder = number_format($subTotalOrder, 2, '.', ' ');
        $this->view->points = $points;
        $this->view->iva = $iva;
        $this->view->shipPrice = number_format($shipPrice, 2, '.', '');    
        $this->view->hasDiscount = $hasDiscount;
        $this->view->businessName = $dataCart->getBusinessName();
        
        $this->view->pointsToNextDiscount = $pointsToNextDiscount;
        $this->view->nextDiscount = number_format($nextDiscount * 100, 2, '.', ' ');
        
        $this->view->discounts = $discounts;
        
        $breadcums = array(
            'product' => array('label' => 'Tienda', 'url' =>'/product'),
            'quick-cart' => array('label' => 'Compra Rápida', 'url' => '')
        );
        
        $this->view->breadcums = $breadcums;
    }
    
    public function saveAction(){
        if(!$this->getRequest()->isPost()) {
            $this->_redirect('product/quick-cart');
        }
        $dataCart = Store_Cart_Factory::createInstance();
        $params = $this->getAllParams();
        
        $perceptionData = null;
        if(isset($this->_config['app']['perception'][$this->_businessman['sigpais']]))
            $perceptionData = $this->_config['app']['perception'][$this->_businessman['sigpais']];
        
        //Validacion de data Base
        $idAddress = $this->getParam('address',0); 
        $vType = $this->getParam('voucher',0); 
        $paymethod = $this->getParam('paymethod',0); 

        
        if ($idAddress == 0 || $vType == 0 || $paymethod == 0) {
            $msg = "";
            if($idAddress == 0) $msg .= "Debe seleccionar una dirección de envío. ";
            if($vType == 0) $msg .= "Debe seleccionar un tipo de comprobante. ";
            if($paymethod == 0) $msg .= "Debe seleccionar un método de pago. ";

            $this->_flashMessenger->error($msg);
            $this->_redirect('product/quick-cart');
        }

        $cardCode = $this->getParam('cardcode',0); 
        $transCode = $this->getParam('transcode', ''); 

        $razonSocial = $this->getParam('businessName', ''); 
        $ruc = $this->getParam('ruc', '');

        //Actualizar Items del Carrito
        foreach($this->getParam('txtcant', array()) as $key => $value) {
            if ($value > 0) $dataCart->updateQuantity($key, $value, true);
            else $dataCart->remove($key);
        }
        $dataCart->calculateTotals();

        if ($dataCart->getProducts()->count() <= 0) {
            $this->_flashMessenger->error('El carrito está vacío');
            $this->_redirect('product/quick-cart');
            return;
        }
        
        $dataCart->setIdAddress($idAddress);
        $dataCart->setStep(6);
        $this->redirect('cart/pay');

        $mAddress = new Businessman_Model_Address();
        $mUbigeo = new Businessman_Model_Ubigeo();

        if($dataCart->getIdAddress() == -1) {
            $address = 'SHOP';
            $ubigeo = $mUbigeo->findById($this->_businessman['codpais'], $this->_businessman['codubig']);
        } else {
            $address = $mAddress->findById($dataCart->getIdAddress());
            $ubigeo = $mUbigeo->findById($address['codpais'], $address['codubig']);  
        }            
        
        //ShipPrice
        $shipPrice = 0;
        $shipType = 'shop';
        if($address != 'SHOP') {
            $shipPriceObj = $mShipPrice->getByUbigeo($address['codubig'], $address['codpais']);
            $shipPrice = isset($shipPriceObj['monflet']) ? $shipPriceObj['monflet'] : 0;
            $shipPrice = $shipPrice * (1 + $dataCart->getIva());
            $shipType = 'normal';
        }
        
        $dataCart->setShipPrice($shipPrice);
        $dataCart->setShipType($shipType);
         
        //Voucher y Percepcion
        $dataCart->setVoucherType($params['voucher']);
        $dataCart->setBusinessName($params['businessName']);
        $dataCart->setDoc($params['ruc']);
                
        $shipPrice = $dataCart->getShipPrice();
        $iva = $dataCart->getIva();
        $totalItems = $dataCart->getTotal();
        $totalPoints = $dataCart->getTotalPoints();
        $discount = $dataCart->getDiscountP();
        $totalItems = ($totalItems - ($totalPoints * ($discount))) * (1 + $iva);
        $subTotalOrder = $totalItems + $shipPrice;
        
        $perception = 0;
        if($perceptionData != null) {
            $selPerception = $perceptionData[$vType];
            $perception = $totalItems > $selPerception['start'] ? $selPerception['perception'] : 0;
        }
        $dataCart->setPerception($perception);
                
        //Registro de Orden
        $dataCart->setCurrencySimbol($this->_businessman['simbolo']);
        $dataCart->setCurrencyDolarFactor($this->_businessman['dolpais']);
        $dataCart->setCurrencyCode($this->_businessman['codint']);
        $dataCart->setDataBusinessman($this->_businessman);
        $dataCart->setCardCode($cardCode);           
        $dataCart->setTransCode($transCode);           
        $dataCart->setPayMethod($paymethod);           
        $orderHelper = $this->getHelper('sendOrder');
        if (!$orderHelper->sendOrder($this->_businessman, null, $address, $ubigeo, $dataCart, $this->_config, 'OFFICE')) {
            $this->view->message = 'Ocurrió un error al realizar el pago de la orden.';
            $this->_flashMessenger->error("Problema de conexión.", 'TEMP');
            $this->view->ok = $ok;
            return;
        }
        $this->view->idOrder = $dataCart->getIdOrder();
        $orderMailHelper = $this->getHelper('orderMail');

        //Pago
        $pay = new Core_Pay_Pago($paymethod, $dataCart, $this->_config['pasarela'], 
                 $this->_businessman['codpais']);            
        
        if($paymethod!='TP004'){
            
        }
    }
    public function ajaxSetAddressAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true); 
        
        $params = $this->getAllParams();
        $msg = "";
        
        $iva = $this->_businessman['iva'];
        $shipPrice = 0;
        $idAddress = "";
        $desAddress = "";
        try {
            $mAddress = new Businessman_Model_Address();
            if (!isset($params['iddire']) || $params['iddire'] == 0) {
                $params['codubig'] = $params['ubigeo_'.$this->_businessman['nivpais']];
                $params['codempr'] = $this->_businessman['codempr'];
                $params['codpais'] = $this->_businessman['codpais'];
                $idAddress = $mAddress->insert($params);
                $msg = "Se Registró Correctamente la dirección. ";
            } else 
                $idAddress = $params['iddire'];
            $dataCart = Store_Cart_Factory::createInstance();
            $dataCart->setIdAddress($idAddress);
            
            $mUbigeo = new Businessman_Model_Ubigeo();
            
            if(!empty($idAddress) && $idAddress != -1) {
                $address = $mAddress->findByIdExtend($idAddress);
                $ubigeo = $mUbigeo->findById($address['codpais'], $address['codubig']);  
                $iva = $ubigeo['ivaubig'] == null ? $this->_businessman['iva'] : $ubigeo['ivaubig'];
                
                $desAddress = (!empty($address['destvia'])) ? 
                                $address['abbrtvia'].' '.$address['desdire'].' '.$address['numdire'].' Int. '.$address['intdire']
                                : $address['desdire'];
                $desAddress .= " - ".$address['ubigeoName']." ";
                if (!empty($address['refdire'])) $desAddress .= "(".$address['refdire'].") ";
                $desAddress .= "/ Teléfono: ".$address['telefono']." / Contacto: ".$address['nomcont'];
            
            
                $mShipPrice = new Shop_Model_ShipPrice();
                $shipPriceObj = $mShipPrice->getByUbigeo($address['codubig'], $address['codpais']);
                $shipPrice = isset($shipPriceObj['monflet']) ? $shipPriceObj['monflet'] : 0;
                
                $desAddress .= "/ Teléfono: ".$address['telefono']." / Contacto: ".$address['nomcont'];
            }  else {
                $desAddress = "Entrega en tienda.";
            }
            
            $dataCart->setIva($iva);
            
            $msg .= "Se seleccionó correctamente la dirección";
            $state = 1;
        } catch (Exception $ex) {
            $msg = "Error de Conexión.";
            $state = 0;
        }
        
        $return = array(
            'state' => $state, 
            'msg' => $msg,
            'idaddress' => $idAddress,
            'description' => $desAddress,
            'iva' => $iva,
            'shipPrice' => $shipPrice
        );
        
        $this->getResponse()            
            ->setHttpResponseCode(200)
            ->setHeader('Content-type', 'application/json; charset=UTF-8', true)
            ->appendBody(json_encode($return));
    }
    
    public function ajaxGetProductsAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true); 
        
        $params = $this->getAllParams();
        $msg = "";
        $state = 0;
        $products = array();
        try {
            $mProduct = new Shop_Model_Product();
            $dataCart = Store_Cart_Factory::createInstance();
            $idsCart= $dataCart->getIdsProduct();
            $products = $mProduct->getByType(
                    $params['category'],
                    $this->_businessman['codpais'], 
                    $params['search'] ,
                    false,
                    false,
                    true,
                    $this->_businessman['codempr'],
                    true,
                    $idsCart
                );
            
            $state = 1;
            $msg = "Ok.";
        } catch (Exception $ex) {
            $msg = "Ocurrió un error.";
        }
        
        $return = array(
            'state' => $state, 
            'msg' => $msg,
            'products' => $products
        );
        
        $this->getResponse()            
            ->setHttpResponseCode(200)
            ->setHeader('Content-type', 'application/json; charset=UTF-8', true)
            ->appendBody(json_encode($return));
    }
    
}
