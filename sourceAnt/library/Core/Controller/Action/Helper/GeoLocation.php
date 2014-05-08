<?php

class GeoLocation {
	private $serviceLocateURL = 'http://api.hostip.info/?ip=';
 
	public function getLocationFromIp()
	{
		$ip = $this->getIpAdress();
 
		if (empty($ip))
			throw new Exception('Error retrieving IP address');
 
		// Use the method your server supports ( most of them only support curl )
		$xmlData = geoLocateIp::file_get_contents_curl($this->serviceLocateURL.$ip);
		//$xmlData = file_get_contents($this->serviceLocateURL.$ip);
 
		if (empty($xmlData))
			throw new Exception('Error retrieving xml');
 
		$locationInfo = $this->parseLocationData($xmlData);
 
		return $locationInfo;
	}
 
	private function getIpAdress(){
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                       $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
            elseif (isset($_SERVER['HTTP_VIA'])) {
               $ip = $_SERVER['HTTP_VIA'];
            }
            elseif (isset($_SERVER['REMOTE_ADDR'])) {
               $ip = $_SERVER['REMOTE_ADDR'];
            }
            else {
               $ip = NULL;
            }
            return $ip;
        }
 
	private function parseLocationData($xmlData)
	{
            // Use of Simple XML extension of PHP 5
            $xml = simplexml_load_string($xmlData);

            if (!is_object($xml))
                    throw new Exception('Error reading XML');

            $infoHost = $xml->xpath('//gml:featureMember');
            $city = $xml->xpath('//gml:featureMember//gml:name');

            $coordinates = $infoHost[0]->xpath('//gml:coordinates');
            $coordinates = split(',', (string) $coordinates[0]);

            $info = array (
                    "City"		=> (string) $city[0],
                    "CountryName"	=> (string) $infoHost[0]->Hostip->countryName,
                    "CountryCode"	=> (string) $infoHost[0]->Hostip->countryAbbrev,
                    "Longitude"	=> $coordinates[0],
                    "Latitude"	=> $coordinates[1]
            );

            return $info;
	}
 
	public static function file_get_contents_curl($url)
	{
		$ch = curl_init();
 
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
 
		$data = curl_exec($ch);
		curl_close($ch);
 
		return $data;
	}
}
