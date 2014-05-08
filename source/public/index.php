<?php
date_default_timezone_set('America/Lima');
ini_set('session.cookie_domain', $_SERVER["SERVER_NAME"]);

defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(__DIR__ . '/../application'));


defined('APPLICATION_PUBLIC')
    || define('APPLICATION_PUBLIC', realpath(__DIR__ . '/'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV',
        (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));


// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR,
        array(
        realpath(APPLICATION_PATH . '/../library'),
        get_include_path(),
    )));

/** Zend_Application */
require_once 'Zend/Application.php';
class index
{
    /**     
     * 
     * @var Zend_Application
     */
    protected $_application = null;

    /**
     * 
     * @var Boolean
     */
     public static $_runBoostrap = true;

    /**
     *
     * @var array
     */
    protected static $_ini = array('routes.ini','images.ini','private.ini',);
    //protected static $_ini = array('routes.ini','images.ini');

    /**
     *
     * @var string
     */
    protected static $_pathConfig = '/configs/';

    /**
     * 
     * @return Zend_Application
     */
    public static function getApplication()
    {
        $application = new Zend_Application(
                APPLICATION_ENV
        );             
        $applicationini = new Zend_Config_Ini(APPLICATION_PATH . "/configs/application.ini", APPLICATION_ENV);        
        $options = $applicationini->toArray();  
        //var_dump($options); exit;
        $webs = $options['websites'];
        $domain = "landing";
        $domainFound = false;        
        $hostDomain = $_SERVER['HTTP_HOST'];
        defined('HOST_DOMAIN')
            || define('HOST_DOMAIN', $hostDomain);        
        //idendificacion de dominio y subdominio
        foreach($webs as $name => $web) {
            if($hostDomain == $web) {
                $domain = $name;
                $domainFound = true;
            }
        }
        $subDomain = '';
        if(!$domainFound) {
            $arrDom = explode('.', $hostDomain);
            if(count($arrDom) > 1) {
                $subDomain = $arrDom[0];
                unset($arrDom[0]);
                $hostDomain = implode('.', $arrDom);
                foreach($webs as $name => $web) {
                    if($hostDomain == $web) {
                        $domain = $name;
                        $domainFound = true;
                        
                    }
                }
            }
            
        }        
        Zend_Registry::set('subdomain',$subDomain);
        
        $iniFile = APPLICATION_PATH . "/configs/".$domain.".ini";
        if (is_readable($iniFile)) {
            $config = new Zend_Config_Ini($iniFile, APPLICATION_ENV);
            
            $options = $application->mergeOptions($options, $config->toArray());
        } else {
            throw new Zend_Exception('Error al cargar '.$domain.'.ini');
        }
        Zend_Registry::set('domain',$domain);
        
//        foreach (self::$_ini as $value) {
//            $iniFile = APPLICATION_PATH . self::$_pathConfig . $value;
//            
//            if (is_readable($iniFile)) {
//                $config = new Zend_Config_Ini($iniFile);
//                $options = $application->mergeOptions($options,
//                    $config->toArray());
//            } else {
//            throw new Zend_Exception('error en la configuracion de los .ini');
//            }
//        }         
        
        Zend_Registry::set('config',$options);       
        $a=$application->setOptions($options);  
        
//        Zend_Debug::dump($a);
        return $application;
    }

    public static function getIni()
    {
        return self::$_ini;
    }

    public static function getPath()
    {
        return self::$_pathConfig;
    }

}
 $application = Index::getApplication()->bootstrap();  
if (Index::$_runBoostrap && !defined('CONSOLE')) {  
    $application->run();         
}
