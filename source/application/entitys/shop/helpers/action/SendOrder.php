<?php

class Shop_Action_Helper_SendOrder extends Zend_Controller_Action_Helper_Abstract {

    public function sendOrder($businessman, $joined, $address, $ubigeo, $carrito, $config, $module = 'LANDING') {
        /*********                      
         Flujo para crear pedido
        *********/

        $orderData = array(
            'fecPedi' => '',
            'nroPedi' => '',
            'tipPedi' => 'I',
            'codEmpr' => $businessman['codempr'],   
            'empPedi' => $businessman['codempr'],
            'codsema' => '',
            'codubig' => $ubigeo['codubig'], 
            'codpais' => $businessman['codpais'],
            'estpedi' => 'PEN',
            'codtpag' => $carrito->getPayMethod(),
            'nopmpag' => $carrito->getTransCode(),
            'monmpag' => $carrito->getTotalOrder()
        );


        if ($address == 'SHOP') {
//            $shipAddressData = array(
//                'idpedi' => '',
//                'codtvia' => '',
//                'ubidenv' => '',
//                'refdenvio' => '',
//                'codubig' => $ubigeo['codubig'], 
//                'codpais' => $businessman['codpais'],
//                'tipdesp' => 'DxOF', 
//                'teldesp' => '', 
//                'celdesp' => ''
//            );
            $shipAddressData = null;
        } else {
            $shipAddressData = array(
                'idpedi' => '',
                'codtvia' => 'CL001',//$address['codtvia'],
                'ubidenv' => isset($address['ubidenv']) ? $address['ubidenv'] : $address['desdire'].' '.$address['numdire'].' Int. '.$address['intdire'],
                'refdenvio' => isset($address['refdenvio']) ? $address['refdenvio'] : $address['refdire'],
                'codubig' => $ubigeo['codubig'], 
                'codpais' => $businessman['codpais'],
                'tipdesp' => 'DxOF', 
                'teldesp' => '', 
                'celdesp' => ''
            );
        }
        $items = array();
        $itemList = $carrito->getProducts()->getIterator();
        $i = 1;
        foreach($itemList as $item) {
            $totalProd = $item->getProduct()->getPrice() + 
                $ubigeo['ivaubig'] * $item->getProduct()->getPrice();

            $detailItem = array(
                'idDPed' => '',
                'codigoNumb' => $i,
                'codigoNumbFac' => '',
                'codigoProd' => $item->getProduct()->getId(),
                'descriProd' => $item->getProduct()->getName(),
                'impuesProd' => $ubigeo['ivaubig'],
                'costosProd' => $item->getProduct()->getPrice(),
                'puntosProd' => $item->getProduct()->getPoints()*$item->getQuantity(),
                'cantiProd' => $item->getQuantity(),
                'totalProd' => 0,
                'puntosIniProd' => 0,
                'totalIniProd' => null,
                'importe' => 0

            );

            $items[] = $detailItem;
        }
        /*
        var_dump($orderData); 
        var_dump($shipAddressData); 
        var_dump($items); 
        */


        $orderData = Zend_Json_Encoder::encode($orderData);
        $shipAddressData = $shipAddressData != null ? Zend_Json_Encoder::encode($shipAddressData) : '';
        
        $items = Zend_Json_Encoder::encode($items);

        /*
        echo $orderData."<br><br>";
        echo $shipAddressData."<br><br>";
        echo $items."<br><br>";
        echo ($joined != null ? $joined->idcliper : '0')."<br><br>";
        exit;
        */
        try { 
            if (true) :
            $url = $config['app']['ws']['orderRegister']['url'];

            $wsParams = array(
                'arg0' => $orderData,
                'arg1' => $shipAddressData,
                'arg2' => $items,
                'arg3' => $joined != null ? $joined->idcliper : 0
            );
            //var_dump($wsParams); exit;            
            
            $logName = 'orderservice';
            if ($module == 'LANDING') $logName = 'orderservicelanding';

            $stream = @fopen(LOG_PATH.'/'.$logName.'.log', 'a', false);
            if (!$stream) {
                echo "Error al guardar.";
            }
            $writer = new Zend_Log_Writer_Stream(LOG_PATH.'/'.$logName.'.log');
            $logger = new Zend_Log($writer);
            $logger->info('***********************************');
            $logger->info('Codigo Empresario: '.$businessman['codempr']);
            $logger->info('arg0: '.$wsParams['arg0']);
            $logger->info('arg1: '.$wsParams['arg1']);
            $logger->info('arg2: '.$wsParams['arg2']);
            $logger->info('arg3: '.$wsParams['arg3']);
            //$logger->info('Modulo: '.$module);
            $logger->info('Puntos: '.$carrito->getPoints());
            
            $soap = new SoapClient($url);
            $result = $soap->generarPedido($wsParams);
            //var_dump($result); exit;

            $str = $result->return;
            $logger->info('Result: '.$str);
            
            $jsonResult = Zend_Json_Decoder::decode($str);
            //var_dump($jsonResult); exit;
            //echo $jsonResult."<br><br>"; exit;
            
            if (empty($jsonResult['iddpedi'])) {
                $logger->err('NO SE OBTUVO CODIGO DE ORDEN.');
                $logger->info('***********************************');
                $logger->info('');
                return false;
            }
            
            $idOrder = $jsonResult['iddpedi'];
            //$idOrder = empty($jsonResult['iddpedi']) ? mt_rand(10000, 99999) : $jsonResult['iddpedi'];
            
            $carrito->setIdOrder($idOrder);
            $carrito->setBizpay($jsonResult['bizpay']);
            //$carrito->setShipPrice($jsonResult['preflete']);
            else :
            $carrito->setShipPrice(5);
            $carrito->setIdOrder(mt_rand(10000, 99999));
            endif;
            $carrito->setIva($ubigeo['ivaubig']);
            $carrito->setUbigeo($ubigeo['codubig']);
            
            $logger->info('idOrden: '.$idOrder);
            $logger->info('***********************************');
            $logger->info('');
            
            $orderCache = array('businessman' => $businessman, 'joined' => $joined, 'module' => $module);
            
            $mOrderDataTemp = new Shop_Model_OrderDataTemp();
            $dataTemp = array(
                'bizpay' => $carrito->getBizpay(), 
                'idpedi' => $idOrder,
                'data' => Zend_Json_Encoder::encode($orderCache)
            );
            $mOrderDataTemp->insert($dataTemp);
            
//            $cache = Zend_Registry::get('Cache');
//            $orderCache = array('businessman' => $businessman, 'joined' => $joined, 'module' => $module);
//            $cache->save($orderCache, 'order_'.$carrito->getIdOrder().'_order');
            
        } catch (Exception $ex) {
            throw new Exception('Ocurrio un errór al registrar el pedido');
            return false;
        }
//        
        return true;
    }
    
    public function sendBill($businessman, $carrito, $config) {
        return true;
        /*
        $joined = $carrito->getDataJoined();
        $billData = array(
            'idpedi' => $carrito->getIdOrder(),
            'codpais' => $businessman['codpais'],
            'codubig' => $carrito->getUbigeo(),
            'codtdov' => $carrito->getVoucherType(),
            'ivadven' => $carrito->getIva(),
            'idcli' => $joined != null ? $joined->idcliper : 0,
            'serdven' => 'XXX',
            'numTarjeta' => $carrito->getCardCode()
        );
        
        $billData = Zend_Json_Encoder::encode($billData);
        
        //echo $billData."<br><br>";
        
        try { 
            $url = $config['app']['ws']['orderRegister']['url'];

            $wsParams = array(
                'arg0' => $billData
            );

            $soap = new SoapClient($url);

            $result = $soap->generarFactura($wsParams);
            
            //var_dump($result); exit; 

            $str = $result->return;
            $jsonResult = Zend_Json_Decoder::decode($str);
            //var_dump($jsonResult); exit;
            
            
            $stream = @fopen(LOG_PATH.'/billservice.log', 'a', false);
            if (!$stream) {
                echo "Error al guardar.";
            }
            $writer = new Zend_Log_Writer_Stream(LOG_PATH.'/billservice.log');
            $logger = new Zend_Log($writer);
            $logger->info('***********************************');
            $logger->info('Codigo Empresario: '.$businessman['codempr']);
            $logger->info('Codigo Pedido: '.$carrito->getIdOrder());
            $logger->info('Bill Data Send: '.$billData);
            $logger->info('Result: '.$str);
            
            //echo $jsonResult."<br><br>"; exit;
            if(empty($jsonResult['iddven'])) {
                //enviar correo o guardar log
                $logger->err('NO SE OBTUVO CODIGO DE VENTA.');
            }
            
            $logger->info('***********************************');
            $logger->info('');
        } catch (Exception $ex) {
            //enviar correo o guardar log
        }
        
        return true; */
    }
}
