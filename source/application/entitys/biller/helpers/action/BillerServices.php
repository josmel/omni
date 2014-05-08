<?php

class Biller_Action_Helper_BillerServices extends Zend_Controller_Action_Helper_Abstract {
    public function getDataBusinessman($idBusinessman, $idWeek, $config) {
        try { 
            $url = $config['url'].$config['dataempresario'];
            $url = str_replace('__IDBUSINESSMAN__', $idBusinessman, $url);
            $url = str_replace('__IDWEEK__', $idWeek, $url);
/*
            //echo $url; exit;
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url); 
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_TIMEOUT, '3');
            $content = trim(curl_exec($curl));
            curl_close($curl);
            
            //echo $content; exit;
            $jsonResult = Zend_Json_Decoder::decode($content);
            //var_dump($jsonResult); exit;
            //echo $jsonResult."<br><br>"; exit;
            
            return $jsonResult;
*/
            return $this->getJsonByCurl($url);
        } catch (Exception $e) {
            echo "Ocurrio un error de conexión a los web services. Inténtelo nuevamente."; exit;
            return false;
        }
        
        return true;
    }

    public function getQualifiedBusinessman($idBusinessman, $idWeek, $config)
    {
    	try {

    		$url = $config['url'].$config['empresarioCalificado'];
            $url = str_replace('__IDBUSINESSMAN__', $idBusinessman, $url);
            $url = str_replace('__IDWEEK__', $idWeek, $url);

            return $this->getJsonByCurl($url);
    	
        } catch (Exception $e) {
    	
        	echo "Ocurrio un error de conexión. Inténtelo nuevamente."; exit;
            return false;
    	
        }
        
        return true;
    
    }

    public function getSalesPreferredCustomers($idBusinessman, $idWeek, $config)
    {
    	try {

    		$url = $config['url'].$config['ventasClientesPreferentes'];
            $url = str_replace('__IDBUSINESSMAN__', $idBusinessman, $url);
            $url = str_replace('__IDWEEK__', $idWeek, $url);

            return $this->getJsonByCurl($url);
    	
        } catch (Exception $e) {
    	
        	echo "Ocurrio un error de conexión. Inténtelo nuevamente."; exit;
            return false;
    	
        }
        
        return true;
    
    }

    public function getBusinessmanIndicators($idBusinessman, $idWeek, $config)
    {
    	try {

    		$url = $config['url'].$config['indicadoresEmpresario'];
            $url = str_replace('__IDBUSINESSMAN__', $idBusinessman, $url);
            $url = str_replace('__IDWEEK__', $idWeek, $url);

            return $this->getJsonByCurl($url);
    	
        } catch (Exception $e) {
    	
        	echo "Ocurrio un error de conexión. Inténtelo nuevamente."; exit;
            return false;
    	
        }
        
        return true;
    
    }

    public function getPatrocinados($idBusinessman, $idWeek, $config)
    {
    	try {

    		$url = $config['url'].$config['getPatrocinados'];
            $url = str_replace('__IDBUSINESSMAN__', $idBusinessman, $url);
            $url = str_replace('__IDWEEK__', $idWeek, $url);
            return $this->getJsonByCurl($url);
    	
        } catch (Exception $e) {
    	
        	echo "Ocurrio un error de conexión. Inténtelo nuevamente."; exit;
            return false;
    	
        }
        
        return true;
    
    }
    
    
     public function getBusinessmanRange($idBusinessman, $idWeek, $config)
    {
    	try {

    		$url = $config['url'].$config['getRango'];
            $url = str_replace('__IDBUSINESSMAN__', $idBusinessman, $url);
            $url = str_replace('__IDWEEK__', $idWeek, $url);
            return $this->getJsonByCurl($url);
    	
        } catch (Exception $e) {
    	
        	echo "Ocurrio un error de conexión. Inténtelo nuevamente."; exit;
            return false;
    	
        }
        
        return true;
    
    }

    public function getVBusinessMan($idBusinessman, $idWeek, $config)
    {
    	try {

    		$url = $config['url'].$config['getVBusinessMan'];
            $url = str_replace('__IDBUSINESSMAN__', $idBusinessman, $url);
            $url = str_replace('__IDWEEK__', $idWeek, $url);

            return $this->getJsonByCurl($url);
    	
        } catch (Exception $e) {
    	
        	echo "Ocurrio un error de conexión. Inténtelo nuevamente."; exit;
            return false;
    	
        }
        
        return true;
    
    }    

    public function updateBusinessMan($idBusinessman, $idWeek, $config)
    {
    	try {

    		$url = $config['url'].$config['updateEmpresario'];
            $url = str_replace('__IDBUSINESSMAN__', $idBusinessman, $url);
            $url = str_replace('__IDWEEK__', $idWeek, $url);

            return $this->getJsonByCurl($url);
    	
        } catch (Exception $e) {
    	
        	echo "Ocurrio un error de conexión. Inténtelo nuevamente."; exit;
            return false;
    	
        }
        
        return true;
    
    }

    public function getBusinessman($idBusinessman, $idWeek, $config)
    {
    	try {

    		$url = $config['url'].$config['getEmpresario'];
            $url = str_replace('__IDBUSINESSMAN__', $idBusinessman, $url);
            $url = str_replace('__IDWEEK__', $idWeek, $url);

            return $this->getJsonByCurl($url);
    	
        } catch (Exception $e) {
    	
        	echo "Ocurrio un error de conexión. Inténtelo nuevamente."; exit;
            return false;
    	
        }
        
        return true;
    
    }


    private function getJsonByCurl($url)
    {
    	 	$curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url); 
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_TIMEOUT, '3');
            $content = trim(curl_exec($curl));
            curl_close($curl);
            
            $jsonResult = Zend_Json_Decoder::decode($content);
            
            return $jsonResult;
    }
}
