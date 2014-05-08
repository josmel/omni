<?php

class Office_CartController extends Core_Controller_ActionOffice
{

    public function init() {
        parent::init();
    }

    public function indexAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true); 
        $dataCart = Store_Cart_Factory::createInstance();
        $this->validateStep(0, $dataCart);
        $this->redirect('/cart/step1'); 
    }
    
    public function step1Action() {
        // action body
        $mDiscount = new Shop_Model_Discount();
        $dataCart = Store_Cart_Factory::createInstance();
        $this->validateStep(1, $dataCart);
        
        if($this->getRequest()->isPost()){

            $params = $this->getAllParams();
            
            foreach($this->getParam('txtcant', array()) as $key => $value) {
                if($value > 0) $dataCart->updateQuantity($key, $value, true);
                else $dataCart->remove($key);
            }
            $dataCart->calculateTotals();
            $dataCart->setHistoryPoins((int) $this->_businessman['historyPoints']);
            //var_dump($dataCart->getHistoryPoins()); exit;
            if ($dataCart->getProducts()->count() <= 0) 
                $this->_flashMessenger->warning('El carrito está vacío', 'TEMP');
            else {
                //$this->_flashMessenger->success("El carrito fue validado correctamente");
                $dataCart->setStep(2);
                $this->redirect('cart/step2');
            }
        }
        
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
        
        $minPoints = $this->_config['app']['pointsToDiscount'];
        $historyPoints = $dataCart->getHistoryPoins();
        $hasDiscount = true;
        
        if (($points + $historyPoints) < $minPoints) {
            $hasDiscount = false;
            $pointsToNextDiscount = $minPoints - ($points + $historyPoints);
            $discountP = 0;
        }
        
        $this->addYosonVar('discounts', Zend_Json_Encoder::encode($discountsYoson), false);
        $this->addYosonVar('currency', $this->_businessman["simbolo"]);
        
        $this->addYosonVar('minPoints', $minPoints);
        $this->addYosonVar('historyPoints', $historyPoints);
        
        if($discountP == -1) $discountP = 0;
        
        
        $iva = $this->_businessman['iva'];
        $subTotal = $dataCart->getTotal();
        $igv = $subTotal * $iva;
        $total = $subTotal + $igv;
        $totalPoints = $dataCart->getTotalPoints();
        $discount = $totalPoints * $discountP * (1 + $iva);
        $totalDiscount = $total - $discount;
             
        $this->view->subTotal = number_format($subTotal, 2, '.', '');
        $this->view->igvTotal = number_format($igv, 2, '.', '');
        $this->view->total = number_format($total, 2, '.', '');
        $this->view->totalDiscount = number_format($totalDiscount, 2, '.', '');
        $this->view->discount = number_format($discount, 2, '.', '');
        $this->view->discountP = number_format($discountP * 100, 2, '.', '');
        $this->view->totalWeight = $dataCart->getWeight();
        $this->view->totalPoints = $totalPoints;
        $this->view->points = $points;
        $this->view->iva = $iva;
        
        $this->view->pointsToNextDiscount = $pointsToNextDiscount;
        $this->view->nextDiscount = number_format($nextDiscount * 100, 2, '.', ' ');
        
        $this->view->discounts = $discounts;
        $this->view->hasDiscount = $hasDiscount;
        
        $breadcums = array(
            'product' => array('label' => 'Tienda', 'url' =>'/product'),
            'step1' => array('label' => 'Carrito de Compra', 'url' => '')
        );
        
        $this->view->breadcums = $breadcums;
    }
            
    public function step2Action() {
        $dataCart = Store_Cart_Factory::createInstance();
        $this->validateStep(2, $dataCart);
        $isNew = false;
        $countryLevels = array();
        for ($i = 1 ; $i <= $this->_businessman['nivpais']; $i++) 
            $countryLevels[] = $this->_businessman['nomniv'.$i];
        
        $formAddress = new Businessman_Form_Address(
                $this->_businessman['codpais'], 
                $this->_businessman['nompais'], 
                $countryLevels
        );
        
        $yosonLevelNames = array();
        foreach ($countryLevels as $cLevel) $yosonLevelNames[] = "-".$cLevel."-";
        $this->addYosonVar('levelNames', Zend_Json_Encoder::encode($yosonLevelNames), false);
        
        $formAddress->addElement('hash', 'tokenHash', array('salt' => 'Solicitud no válida.'));
        
        if ($this->_businessman['codpais'] == '840') {
            $formAddress->setDecorators(array(array('ViewScript',
                array('viewScript'=>'forms/_formAddressUSA.phtml'))));
        } else {
            $formAddress->setDecorators(array(array('ViewScript',
                array('viewScript'=>'forms/_formAddress.phtml'))));
        }
        
        
        $mAddress = new Businessman_Model_Address();
        $addresses = $mAddress->findAllByBusinessman($this->_businessman['codempr'], $this->_businessman['codpais']); //13901
        $selAddress = null;

        $zipValidatorHelper = $this->getHelper('zipCodeValidate');
        
        //Label Referencia
        $lblReference=($this->_businessman['codpais'] == '484')?"Colonia":"Referencia";       
        $this->view->lblReference=$lblReference;
        
        if($this->getRequest()->isPost()) {
            $params = $this->getAllParams();
            $error = false;
            try {
            $points = $dataCart->getPoints();
            $mDiscount = new Shop_Model_Discount();
            $discount = $mDiscount->findByPoints($this->_businessman['codtemp'], $points);
            $discountP = isset($discount['pordesc']) ? $discount['pordesc'] : 0;  
            
            $minPoints = $this->_config['app']['pointsToDiscount'];
            $historyPoints = $dataCart->getHistoryPoins();
            
            if (($points + $historyPoints) < $minPoints) {
                $discountP = 0;
            }
        
            $dataCart->setDiscountP($discountP);
            
            if ($params['address'] == 'new') {
                $isNew = true;
                $level = 1;
                $mUbigeo = new Businessman_Model_Ubigeo();
                $prevName = '';
                $iva = $this->_businessman['iva']; 
                foreach ($formAddress->getUbigeoTree() as $ubigeoName) {
                    if ($level > 1) {
                        $formAddress->getElement('ubigeo_'.$level)->setMultiOptions(
                            $mUbigeo->findAllByCountryPairs(
                                    $this->_businessman['codpais'], //$this->_businessman['codpais'], 
                                    $params[$prevName]
                            )
                        );
                    }
                    $prevName = 'ubigeo_'.$level;
                    $level++;
                }
                        
                $params['codubig'] = $params[$prevName];
                $params['codempr'] = $this->_businessman['codempr'];
                if($formAddress->isValid($params)) {
                    $params['codpais'] = $this->_businessman['codpais'];
                    
                    if (!$zipValidatorHelper->isValid($this->_businessman['codpais'], $params['zipcode'])) {
                        $msg = 'Debe ingresar un código postal.';
                        $this->_flashMessenger->error($msg, 'TEMP');
                        $error = true;
                    } else {
                        $idAddress = $mAddress->insert($params);
                    }
                } else {
                    $selAddress = array();
                    $errorMsgs = Core_FormErrorMessage::getErrors($formAddress);
                    $this->_flashMessenger->error('No se pudo registrar la dirección.', 'TEMP');
                    $this->_flashMessenger->error($errorMsgs, 'TEMP');
                    $error = true;
                    //var_dump($errorMsgs);
                }
                $dataCart->setIva($iva);
            } elseif ($params['address'] == '-1') {
                $iva = $this->_businessman['iva']; 
                $idAddress = $params['address'];      
                $dataCart->setShipPrice(0);
                $dataCart->setShipType('shop');
            
            
                $dataCart->setIva($iva); 
                $dataCart->setIdAddress($idAddress);
                $dataCart->setUbigeo($this->_businessman['codubig']);
                $dataCart->setDollarFactor($this->_businessman['dolpais']);
                $dataCart->setStep(4);
                $this->redirect('/cart/step4');
                
            } else {
                $idAddress = $params['address'];
            }
            } catch (Exception $ex) {
                if (!isset($params['address'])) 
                    $msg = 'Debe seleccionar una dirección';
                else
                    $msg = 'Ocurrió un error.';
                $this->_flashMessenger->error($errorMsgs, 'TEMP');
                $error = true;
            }
            
            if(!$error) {
                $mUbigeo = new Businessman_Model_Ubigeo();
                $address = $mAddress->findById($idAddress);
                
                if(trim($address['codubig']) == '0000000000') {
                    $msg = 'La dirección seleccionada tiene datos incompletos, por favor ingrese una nueva direccion de envío.';
                    $this->_flashMessenger->error($msg, 'TEMP');
                    $error = true;
                } elseif (!$zipValidatorHelper->isValid($this->_businessman['codpais'], $address['zipcode'])) {
                    $msg = 'La dirección seleccionada no tiene código postal.';
                    $this->_flashMessenger->error($msg, 'TEMP');
                    $error = true;
                } else {
                    $selAddress = $address;
                    $ubigeo = $mUbigeo->findById($address['codpais'], $address['codubig']);                        
                    if($ubigeo['ivaubig'] != $this->_businessman['iva'])
                        $this->_flashMessenger->info(
                            'El Impuesto ha variado para su distrito de entrega.'
                            .'El ubigeo es <b>'.$ubigeo['ivaubig'].'</b>'
                        );
                    $dataCart->setIva($ubigeo['ivaubig']);  
                    $dataCart->setIdAddress($idAddress);
                    $dataCart->setUbigeo($address['codubig']);
                    $dataCart->setDollarFactor($this->_businessman['dolpais']);

                    $dataCart->setStep(3);

                    $this->redirect('/cart/step3');
                }
            }
        }
        
        //var_dump($addresses);
        if(!$isNew && empty($selAddress) && count($addresses) > 0) $selAddress = current($addresses);
        
        $this->view->addresses = $addresses;
        $this->view->formAddress = $formAddress;
        $this->view->selAddress = $selAddress;
        $this->view->isNew = $isNew;
        //var_dump($selAddress);
            
        $this->addYosonVar('ubigeoLevels', $this->_businessman['nivpais'], false);
        $this->addYosonVar('addresses', json_encode($addresses), false);
        $this->addYosonVar('streetType', json_encode($formAddress->getElement('codtvia')->getMultiOptions()), false);

        $breadcums = array(
            'product' => array('label' => 'Tienda', 'url' =>'/product'),
            'step1' => array('label' => 'Carrito de Compra', 'url' => '/cart/step1'),
            'step2' => array('label' => 'Dirección de envío', 'url' => '')
        );
        $this->view->breadcums = $breadcums;
    }
    
    public function step3Action() {
        $idCountry = $this->_businessman['codpais'];
        $dataCart = Store_Cart_Factory::createInstance();
        $this->validateStep(3, $dataCart);
        
        if($dataCart->getIdAddress() == -1) $this->redirect('/cart/step2'); 
        
        //$dataCart->setIva(0.18);
        
        $mAddress = new Businessman_Model_Address();
        $mUbigeo = new Businessman_Model_Ubigeo();
        $mShipPrice = new Shop_Model_ShipPrice();
        
        $address = $mAddress->findByIdExtend($dataCart->getIdAddress());
        //$address['ubigeoName'] = 'Lima, Lima, Surco';
        //$address['codubig'] = '0100020002';
        //$address['codpais'] = 604;
        
        $shipPriceObj = $mShipPrice->getByUbigeo($address['codubig'], $address['codpais']);
        
        if($dataCart->getAllPoints() > $shipPriceObj['punfin']) {
            $dataCart->setStep(4);
            $this->redirect('/cart/step4'); 
        }
        
        $shipPrice = isset($shipPriceObj['monflet']) ? $shipPriceObj['monflet'] : 0;
        $shipPrice = $shipPrice * (1 + $dataCart->getIva());
        
        if($this->getRequest()->isPost()) {
            $dataCart->setShipPrice($shipPrice);
            $dataCart->setShipType('normal');
            
            $dataCart->setStep(4);
            $this->redirect('/cart/step4');
        }
        
        $iva = $dataCart->getIva();
        
        $this->view->shipPrice = number_format($shipPrice, 2, '.', '');    
        $this->view->weight = $dataCart->getWeight();
        $this->view->ubigeoName = $address['ubigeoName'];
        
        $breadcums = array(
            'product' => array('label' => 'Tienda', 'url' =>'/product'),
            'step1' => array('label' => 'Carrito de Compra', 'url' => '/cart/step1'),
            'step2' => array('label' => 'Dirección de envío', 'url' => '/cart/step2'),
            'step3' => array('label' => 'Forma de envío', 'url' => '')
        );
        
        $this->view->breadcums = $breadcums;
    }
    
    public function step4Action() {
        $dataCart = Store_Cart_Factory::createInstance();
        $this->validateStep(4, $dataCart);
        $mVType = new Shop_Model_VoucherType();
        $vTypes = $mVType->findAll();
        
        $perceptionData = null;
        if(isset($this->_config['app']['perception'][$this->_businessman['sigpais']]))
            $perceptionData = $this->_config['app']['perception'][$this->_businessman['sigpais']];
         
        
        $this->view->vTypes = $vTypes;
        //var_dump($vTypes);
        $vType = '';
        
        $shipPrice = $dataCart->getShipPrice();
        $iva = $dataCart->getIva();
        $totalItems = $dataCart->getTotal();
        $totalPoints = $dataCart->getTotalPoints();
        $discount = $dataCart->getDiscountP();
        $totalItems = ($totalItems - ($totalPoints * ($discount))) * (1 + $iva);
        $subTotalOrder = $totalItems + $shipPrice;
        
        $this->view->ruc = $this->_businessman['rucempr'];
        $this->view->businessName = $this->_businessman['nomempr'].' '.$this->_businessman['appempr'].' '.$this->_businessman['apmempr'];
        $this->view->isFac = false;
        
        if($this->getRequest()->isPost()) {
            $params = $this->getAllParams();
            if(isset($params['voucher'])) {
                $vType = $params['voucher'];
                $valid = true;
                $ruc = trim($params['ruc']);
                $this->view->ruc = $ruc;
                $params['businessName'] = isset($params['businessName']) ? $params['businessName'] 
                        : $this->_businessman['nomempr'].' '.$this->_businessman['appempr'].' '.$this->_businessman['apmempr'];
                $this->view->businessName = $params['businessName'];
                
                if($vType == 'FAC') {
                    $this->view->isFac = true;
                    $rucValidatorHelper = $this->getHelper('rucValidate');
                    if(!$rucValidatorHelper->isValid($this->_businessman['codpais'], $ruc)) {
                        $valid = false;
                        $this->_flashMessenger->error('El nro. de RUC ingresado no es válido.', 'TEMP');
                    } else {
                        $mBusinessman = new Businessman_Model_Businessman();
                        $mBusinessman->updateRuc($ruc, $this->_businessman['codempr']);
                        $this->_businessman['rucempr'] = $ruc;
                        Zend_Auth::getInstance()->getStorage()->write($this->_businessman);
                    }
                } 
                
                if($valid) {
                    $dataCart->setVoucherType($params['voucher']);
                    $dataCart->setBusinessName($params['businessName']);
                    $dataCart->setDoc($params['ruc']);

                    $perception = 0;
                    if($perceptionData != null) {
                        $selPerception = $perceptionData[$vType];
                        $perception = $totalItems > $selPerception['start'] ? $selPerception['perception'] : 0;
                    }
                    $dataCart->setPerception($perception);

                    $dataCart->setStep(5);
                    $this->redirect('/cart/step5');
                }
            } else {
                $this->_flashMessenger->error('Seleccione un tipo de comprobante de pago', 'TEMP');
            }
        }
               
        if ($vType == '' && count($vTypes) > 0) $vType = $vTypes[0]['codtdov'];
        $selVType = array();
        $perYoson = array();
        foreach ($vTypes as $vt) {
            if($perceptionData != null) {
                $ptPerceptionP = $subTotalOrder > $perceptionData[$vt['codtdov']]['start'] ? $perceptionData[$vt['codtdov']]['perception'] : 0;
                $ptPerception = $subTotalOrder * $ptPerceptionP;
                $ptOrder = $subTotalOrder + $ptPerception;
                
                $perYoson[$vt['codtdov']] = array();
                $perYoson[$vt['codtdov']]['perception'] = number_format(($ptPerceptionP * 100), 2, '.', '');
                $perYoson[$vt['codtdov']]['totalOrder'] = number_format($ptOrder, 2, '.', '');
                $perYoson[$vt['codtdov']]['totalPerception'] = number_format($ptPerception, 2, '.', '');
            }
            
            if ($vt['codtdov'] == $vType) $selVType = $vt;
        } 
        
        $selPerception = array('perception' => 0, 'start' => 0);
        if($perceptionData != null && isset($perceptionData[$vType]))
            $selPerception = $perceptionData[$vType];
        
        
        $perceptionP = $subTotalOrder > $selPerception['start'] ? $selPerception['perception'] : 0;
        $perception = $subTotalOrder * $perceptionP; 
        $total = $subTotalOrder + $perception;
        
        $this->view->shipPrice = number_format($shipPrice, 2, '.', ' ');    
        $this->view->totalItems = number_format($totalItems, 2, '.', ' ');    
        $this->view->perceptionP = number_format(($perceptionP * 100), 2, '.', ' ');    
        $this->view->perception = number_format($perception, 2, '.', ' ');    
        $this->view->total = number_format($total, 2, '.', ' ');    
        $this->view->weight = $dataCart->getWeight();
        $this->view->vType = $vType;
        $this->view->selPerception = $selPerception;
        $this->view->perceptionData = $perceptionData;
        $this->view->subTotalOrder = $subTotalOrder;
        $this->view->selVType = $selVType;
        
        $this->addYosonVar('perception', json_encode($perYoson), false);
        
        $breadcums = array(
            'product' => array('label' => 'Tienda', 'url' =>'/product'),
            'step1' => array('label' => 'Carrito de Compra', 'url' => '/cart/step1'),
            'step2' => array('label' => 'Dirección de envío', 'url' => '/cart/step2'),
            'step3' => array('label' => 'Forma de envío', 'url' => '/cart/step3'),
            'step4' => array('label' => 'Tipo de comprobante', 'url' => '')
        );
        $this->view->breadcums = $breadcums;
    }
    
    public function step5Action() {
        $dataCart = Store_Cart_Factory::createInstance();
        $this->validateStep(5, $dataCart);
        //var_dump($dataCart);
        
        $mVType = new Shop_Model_VoucherType();
        $vType = $mVType->findById($dataCart->getVoucherType());
        
        $mAddress = new Businessman_Model_Address();
        if($dataCart->getIdAddress() == -1) 
            $address = 'SHOP';
        else 
            $address = $mAddress->findByIdExtend($dataCart->getIdAddress());
        $this->view->address = $address;
        //var_dump($address);
        
        $discountP = $dataCart->getDiscountP();
        $iva = $dataCart->getIva();
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
        
        if($this->getRequest()->isPost()) {
            $mAddress = new Businessman_Model_Address();
            $mUbigeo = new Businessman_Model_Ubigeo();
                         
            
            if($dataCart->getIdAddress() == -1) {
                $address = 'SHOP';
                $ubigeo = $mUbigeo->findById($this->_businessman['codpais'], $this->_businessman['codubig']);
            } else {
                $address = $mAddress->findById($dataCart->getIdAddress());
                $ubigeo = $mUbigeo->findById($address['codpais'], $address['codubig']);  
            }
            
            $dataCart->setCurrencySimbol($this->_businessman['simbolo']);
            $dataCart->setCurrencyDolarFactor($this->_businessman['dolpais']);
            $dataCart->setCurrencyCode($this->_businessman['codint']);

            //$orderHelper = $this->getHelper('sendOrder');
            //if ($orderHelper->sendOrder($this->_businessman, null, $address, $ubigeo, $dataCart, $this->_config, 'OFFICE')) {    
                //var_dump($dataCart->getIdOrder());
                $dataCart->setStep(6);
                $dataCart->setTotalOrder($totalOrder);
                //exit;
                $this->redirect('/cart/payment');
            //} else {
            //    $this->_flashMessenger->error("Problema de conexión.", 'TEMP');
            //}
        }
        
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
        
        $this->view->iva = $iva;
        $this->view->shipPrice = number_format($shipPrice, 2, '.', '');    
        
        $this->view->businessName = $dataCart->getBusinessName();
        $this->view->vType = $vType;
                
        $breadcums = array(
            'product' => array('label' => 'Tienda', 'url' =>'/product'),
            'step1' => array('label' => 'Carrito de Compra', 'url' => '/cart/step1'),
            'step2' => array('label' => 'Dirección de envío', 'url' => '/cart/step2'),
            'step3' => array('label' => 'Forma de envío', 'url' => '/cart/step3'),
            'step4' => array('label' => 'Tipo de comprobante', 'url' => '/cart/step4'),
            'step5' => array('label' => 'Consolidación de Pedido', 'url' => '')
        );
        $this->view->breadcums = $breadcums;
    }
    
    public function paymentAction() {
        // action body
        $dataCart = Store_Cart_Factory::createInstance();
        
        $this->validateStep(6, $dataCart);
        //echo $dataCart->getTotalOrder('DOLLAR');
        $idOrder = $dataCart->getIdOrder();
        //var_dump($idOrder);
        $total = $dataCart->getTotalOrder();
                       
        $formPayment = new Shop_Form_Payment(
            $this->_businessman, 0, 0, 0, 0,
            number_format($total, 2, '.', ' '), 
            false
        );
        $formPayment->addElement('hash', 'tokenHash', array('salt' => 'Solicitud no válida.'));
        $formPayment->addDecoratorCustom('forms/_formPayment.phtml');
        $this->view->formPayment = $formPayment;
        
        $breadcums = array(
            'product' => array('label' => 'Tienda', 'url' =>'/product'),
            'step1' => array('label' => 'Carrito de Compra', 'url' => '/cart/step1'),
            'step2' => array('label' => 'Dirección de envío', 'url' => '/cart/step2'),
            'step3' => array('label' => 'Forma de envío', 'url' => '/cart/step3'),
            'step4' => array('label' => 'Tipo de comprobante', 'url' => '/cart/step4'),
            'step5' => array('label' => 'Consolidación de Pedido', 'url' => '/cart/step5'),
            'step6' => array('label' => 'Modo de Pago', 'url' => '')
        );
        $this->view->breadcums = $breadcums;
    }
    
    public function payAction() {    
        $ok = false;
        $dataCart = Store_Cart_Factory::createInstance(); 
        $this->validateStep(6, $dataCart);
        
        $breadcums = array(
            'product' => array('label' => 'Tienda', 'url' =>'/product'),
            'step6' => array('label' => 'Pago del Pedido', 'url' => '')
        );
        $this->view->breadcums = $breadcums;
        
        $cardCode=$this->getParam('cardcode',0); 
        $transCode=$this->getParam('transcode', ''); 
        $paymethod=$this->getParam('paymethod',0); 
        $this->view->message = 'Sin Data';
        if($this->_request->isPost() && !empty($paymethod)) { 
            //Codigo para generar nueva orden.
            $mAddress = new Businessman_Model_Address();
            $mUbigeo = new Businessman_Model_Ubigeo();

            if($dataCart->getIdAddress() == -1) {
                $address = 'SHOP';
                $ubigeo = $mUbigeo->findById($this->_businessman['codpais'], $this->_businessman['codubig']);
            } else {
                $address = $mAddress->findById($dataCart->getIdAddress());
                $ubigeo = $mUbigeo->findById($address['codpais'], $address['codubig']);  
            }                    

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
            
            $pay = new Core_Pay_Pago($paymethod, $dataCart, $this->_config['pasarela'], 
                     $this->_businessman['codpais']);            
            if($paymethod!='TP004'){
                $tableOrder=new Shop_Model_Order();
                $pay->pagar(); 
//              $selectorHelper = $this->getHelper('selectorPay');
//              $selectorHelper->payMethod($paymethod,$pay);
                
                if($pay->isSuccess()) {
                //var_dump(1); exit;
                    $mAddress = new Businessman_Model_Address();
                    
                    if($dataCart->getIdAddress() == -1) $address = 'SHOP';
                    else $address = $mAddress->findByIdExtend($dataCart->getIdAddress()); 
                    
                    $orderHelper = $this->getHelper('sendOrder');
                    if ($paymethod=='TP011') {                    
                        $orderHelper->sendBill($this->_businessman, $dataCart, $this->_config);   
                    }
                    
                    $this->view->payMessage = $pay->getMensaje();
                    $this->view->dateOrder = Core_Utils_Utils::dateFormatToPartialDescription(date('d/m/Y'));
                    $this->view->datePayment = Core_Utils_Utils::dateFormatToPartialDescription(date('d/m/Y'));
                    $this->view->totalOrder = number_format($dataCart->getTotalOrder(), 2, '.', ' ');
                    $orderMailHelper->officeOrderComplete($this->_businessman,
                        $address, $dataCart, $this->_flashMessenger, $this->getHelper('mail'));                
                    $this->view->totalCart = $dataCart->getProducts()->count();
                    $this->view->idAddress = $dataCart->getIdAddress();
                    
                    $mOrderDataTemp = new Shop_Model_OrderDataTemp();
                    $mOrderDataTemp->delete($dataCart->getIdOrder());
                    
                    $dataCart->reset();
                    $dataCart->setStep(1);
                    $ok = true;
                }else{
                    $tableOrder->inactiveOrder($dataCart->getIdOrder());
                }
            }else{
                echo $pay->getMensaje();
                $this->view->message = null;
                exit;
            }
            $this->view->message = $pay->getMensaje();
            //var_dump($pay->getMensaje()); exit;
        } else {
             $this->_flashMessenger->warning("No escogió metodo de pago");
             $this->redirect('/cart/payment');
        }
        
                
        //$this->view->idOrder = $dataCart->getIdOrder();
        $this->view->ok = $ok;
    }
    
    public function alignetAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $dataCart = Store_Cart_Factory::createInstance();
        $dataCart->setDataBusinessman($this->_businessman);
        $dataCart->setCardCode('');                        
        $pay = new Core_Pay_Pago('TP004', $dataCart, $this->_config['pasarela']);   
        $pay->pagar();
        echo $pay->getMensaje();
    }
    
    public function alignetSuccessAction()
    {
        //$this->_helper->layout()->disableLayout();
        //$this->_helper->viewRenderer->setNoRender(true);
        $ok = false;
        $dataCart = Store_Cart_Factory::createInstance();
        
        $mOrderDataTemp = new Shop_Model_OrderDataTemp();
        $dataTemp = $mOrderDataTemp->findByIdOrder($dataCart->getIdOrder());
        $result = Zend_Json_Decoder::decode($dataTemp['data']);
        //var_dump($result);
        if ($result && $result['businessman']['codempr'] == $this->_businessman['codempr']
            && isset($result['alignetResult'])) {
            $alignetResult = $result['alignetResult'];
            $this->view->message = $alignetResult['message'];
            //var_dump($alignetResult);
            if ($alignetResult['alignetSuccess']) {
                $this->view->payMessage = "Pago con tarjeta de crédito.";
                $this->view->dateOrder = Core_Utils_Utils::dateFormatToPartialDescription(date('d/m/Y'));
                $this->view->datePayment = Core_Utils_Utils::dateFormatToPartialDescription(date('d/m/Y'));
                
                $mAddress = new Businessman_Model_Address();
                $ok = true;
                $address = $mAddress->findByIdExtend($dataCart->getIdAddress(), 
                    $this->_businessman['codpais']);                
                $orderHelper = $this->getHelper('sendOrder');
                $orderHelper->sendBill($this->_businessman, $dataCart, $this->_config); 

                //Enviar email de confirmación de compra exitosa
                $orderMailHelper = $this->getHelper('orderMail');
                $orderMailHelper->officeOrderComplete($this->_businessman,
                        $address, $dataCart, $this->_flashMessenger, $this->getHelper('mail'));                
                $this->view->totalOrder = number_format($dataCart->getTotalOrder(), 2, '.', '');
                $this->view->idOrder = $dataCart->getIdOrder();
                $this->view->idAddress = $dataCart->getIdAddress();
                
                $mOrderDataTemp->delete($dataCart->getIdOrder());
                    
                $dataCart->reset();
            } else {
                $mOrder = new Shop_Model_Order();
                $mOrder->inactiveOrder($dataCart->getIdOrder());
            }
        }  else {
            $this->view->message = "Error Desconocido"; 
        }
        $this->view->ok = $ok;
    }
    
    public function nextPaySuccessAction()
    {
        //$this->_helper->layout()->disableLayout();
        //$this->_helper->viewRenderer->setNoRender(true);
        $ok = false;
        $dataCart = Store_Cart_Factory::createInstance();
                    
        $mOrderDataTemp = new Shop_Model_OrderDataTemp();
        $dataTemp = $mOrderDataTemp->findByIdOrder($dataCart->getIdOrder());
        $result = Zend_Json_Decoder::decode($dataTemp['data']);

        //var_dump($result);
        if ($result && $result['businessman']['codempr'] == $this->_businessman['codempr']
            && isset($result['nextPayResult'])) {
            $npResult = $result['nextPayResult'];
            $state = Core_Service_NextPay::getState($npResult['payment_status']);
             
            $this->view->message = Core_Service_NextPay::getMessage($state);
            
            if ($state == Core_Service_NextPay::APPROVED) {
                $ok = true;
                $this->view->payMessage = "Pago con tarjeta de crédito ".$npResult['cardtype'];
                $this->view->dateOrder = Core_Utils_Utils::dateFormatToPartialDescription(date('d/m/Y'));
                $this->view->datePayment = Core_Utils_Utils::dateFormatToPartialDescription(date('d/m/Y'));
                $mAddress = new Businessman_Model_Address();
                $address = $mAddress->findByIdExtend($dataCart->getIdAddress(), 
                    $this->_businessman['codpais']);                
                $orderHelper = $this->getHelper('sendOrder');
                $orderHelper->sendBill($this->_businessman, $dataCart, $this->_config); 

                //Enviar email de confirmación de compra exitosa
                $orderMailHelper = $this->getHelper('orderMail');
                $orderMailHelper->officeOrderComplete($this->_businessman,
                        $address, $dataCart, $this->_flashMessenger, $this->getHelper('mail'));               
                $this->view->totalOrder = number_format($dataCart->getTotalOrder(), 2, '.', ' ');
                $this->view->idOrder = $dataCart->getIdOrder();
                $this->view->idAddress = $dataCart->getIdAddress();
                
                $mOrderDataTemp->delete($dataCart->getIdOrder());
                
                $dataCart->reset();
            } else {
                $mOrder = new Shop_Model_Order();
                $mOrder->inactiveOrder($dataCart->getIdOrder());
            }
        }  else {
            $this->view->message = "Error Desconocido"; 
        }
        $this->view->ok = $ok;
    }
    
    public function ajaxAddProductAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true); 
        $dataCart = Store_Cart_Factory::createInstance();
        
        $pData = array();
        try { 
            $mProduct = new Shop_Model_Product();
            //Ejemplo de implementacion
            $iva = $this->_businessman['iva'];
            $quantityAdd = $this->_getParam('quantity', 1);
            $dataProduct = $mProduct->getById(
                $this->_getParam('idproduct', -1), 
                $this->_businessman['codpais']
            );
            if (!empty($dataProduct)) {
                $description = $dataProduct['codprod'].'**'.$dataProduct['desprod'];
                $urlPicture = $this->_config['app']['imagesProduct'].'catalog/'
                        .$dataProduct['codprod'].'.jpg';
                $pd = new Store_Product(
                        $dataProduct['codprod'], $dataProduct['desprod'], 
                        $dataProduct['shorttext'], $dataProduct['monprec']
                    );
                $pd->setSlug($dataProduct['slug']);
                $pd->setPoints($dataProduct['punprod']);
                $pd->setEnableDiscount($dataProduct['adeprod']);
                $pd->setWeight($dataProduct['pesoprod']);
                $pd->setBoxDescription($dataProduct['desccaja']);

                $pd->setUrlPicture($urlPicture);

                $item = new Store_Cart_Item($pd, $quantityAdd);
                $dataCart->addCart($item);
                //var_dump($carrito->getProducts()->getIterator()); exit;
                $quantity = $dataCart->getQuantity($dataProduct['codprod']);
                $selDataProduct= $dataCart->getContents()->getItem($dataProduct['codprod']);
                $realPrice= $selDataProduct->getPrice();
                $totalPrice = $realPrice * $quantity * (1 + $iva);
                $pData = array(
                    'id'=> $dataProduct['codprod'],
                    'name' => $dataProduct['desprod'],
                    'quantity' => $quantity,
                    'price' => $totalPrice,
                    'realPrice'=> $realPrice,
                    'points'=> $selDataProduct->getPoints(),
                    'discount'=>$selDataProduct->getEnableDiscount(),
                    'url'=>SITE_URL.'product/detail/'.$dataProduct['slug'],
                    'urlPicture' => $urlPicture
                );
                $msg = "Se agregó el producto al carrito.";
                $state = 1;
            } else { 
                $msg = "El producto no existe.";
                $state = 0;
            }
        } catch(Exception $ex) {
            $msg = "Error de conexión.";
            $state = 0;
        }
        
        $dataCart->setHistoryPoins((int) $this->_businessman['historyPoints']);
        $dataCart->setStep(1);
        //var_dump($dataProduct); exit;
        
        $iva = $this->_businessman['iva'];
        $subTotal = $dataCart->getTotal();
        $igv = $subTotal * $iva;
        $total = $subTotal + $igv;
                
        $return = array(
            'state' => $state, 
            'msg' => $msg, 
            'totalOrder' => number_format($total, 2, '.', ''),
            'productData' => $pData,
            'totalItems' => $dataCart->getProducts()->count()
        );
        
        $this->getResponse()            
            ->setHttpResponseCode(200)
            ->setHeader('Content-type', 'application/json; charset=UTF-8', true)
            ->appendBody(json_encode($return));
    }
    
    
    
    public function ajaxRemoveProductAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true); 
        
        //Ejemplo de implementacion
        $dataCart = Store_Cart_Factory::createInstance();

        //var_dump($dataProduct); exit;
        $idProduct = $this->_getParam('idproduct', 0);
        $state = 0;
        
        if($idProduct == 0) {
           $msg = "Debe enviar un código de producto válido.";
        } else {
            if($dataCart->has($idProduct)) {
                $dataCart->remove($idProduct);
                $dataCart->setStep(1);
                $msg = "Se quitó el producto del carrito.";
                $state = 1;
            } else {
                $msg = "El producto no existe dentro del carrito.";
            }
        }
        
        $dataCart->setStep(1);
        $iva = $this->_businessman['iva'];
        $subTotal = $dataCart->getTotal();
        $igv = $subTotal * $iva;
        $total = $subTotal + $igv;
        
        $return = array(
            'state' => $state, 
            'msg' => $msg, 
            'totalOrder' => number_format($total, 2, '.', ''),
            'totalItems' => $dataCart->getProducts()->count()
        );
        
        $this->getResponse()            
            ->setHttpResponseCode(200)
            ->setHeader('Content-type', 'application/json; charset=UTF-8', true)
            ->appendBody(json_encode($return));
    }
    
    public function ajaxEditAddressAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true); 
        
        $params = $this->getAllParams();
        try {
            $mAddress = new Businessman_Model_Address();
            $mAddress->update($params['iddire'], $params);

            $msg = "Se Actualizó Correctamente la dirección.";
            $state = 1;
        } catch (Exception $ex) {
            $msg = "Error de Conexión.";
            $state = 0;
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
    
    private function validateStep($step, $dataCart) {
        $processStep = $dataCart->getStep($step);
        if($step == $processStep) return true;
        if($step > 0 && $processStep > $step) {
            $dataCart->setStep($step);
        } else {
            switch ($processStep) {
                case 1: $this->redirect('/cart/step1'); break;
                case 2: $this->redirect('/cart/step2'); break;
                case 3: $this->redirect('/cart/step3'); break;
                case 4: $this->redirect('/cart/step4'); break;
                case 5: $this->redirect('/cart/step5'); break;
                case 6: $this->redirect('/cart/payment'); break;
                default : if ($step != 1) $this->redirect('/cart/step1'); break;
            }
        }
    }
}
