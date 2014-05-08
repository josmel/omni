<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    public function _initView() {
        
        $this->bootstrap('layout');        
        $layout = $this->getResource('layout');
        $v = $layout->getView();

        $v->addHelperPath(APPLICATION_PATH."/../library/Core/View/Helper", "Core_View_Helper");
            
        $config = Zend_Registry::get('config');
        $version= self::getVersion();

        $cache = $this->getPluginResource('cachemanager')->getCacheManager()->getCache('default');
        Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
        Zend_Registry::set('Cache', $cache);
        
        $this->getResourceLoader()->addResourceType('entity', 'entitys/', 'Entity');
        
        //Definiendo Constante para Partials
        defined('STATIC_URL')
            || define('STATIC_URL', $config['app']['staticUrl']);
        defined('DINAMIC_URL')
            || define('DINAMIC_URL', $config['app']['dinamicUrl']);
        defined('IMG_URL')
            || define('IMG_URL', $config['app']['imgUrl']);
        defined('SITE_URL')
            || define('SITE_URL', $config['app']['siteUrl']);
        defined('SITE_TEMP')
            || define('SITE_TEMP',$config['app']['elementTemp']);
        defined('SITE_VERSION')
            || define('SITE_VERSION',$version);
        defined('STATIC_ADMIN_IMG')
            || define('STATIC_ADMIN_IMG',$config['app']['imgAdmin']);
        
        defined('ROOT_IMG_DINAMIC')
            || define('ROOT_IMG_DINAMIC',$config['app']['rootImgDinamic']);
        defined('LOG_PATH')
            || define('LOG_PATH',$config['app']['logPath']);

        //* Antes de modularizar -solo para el landing *//   
        $uri="http://".Zend_Registry::get('subdomain').'.'.$config['websites']['landing'];  
        if(Zend_Registry::get('domain') == 'landing') {
            defined('STATIC_LANDING_IMG')
                || define('STATIC_LANDING_IMG',$config['app']['imgLanding']);
            defined('CAPTCHA_FONT')
                || define('CAPTCHA_FONT',$config['captcha']['font']);
            defined('CAPTCHA_IMG')
                || define('CAPTCHA_IMG',$config['captcha']['img']);
            defined('CAPTCHA_URL')
                || define('CAPTCHA_URL',$uri.'/captcha');
            defined('URL_FUXION')
                || define('URL_FUXION','http://test.fuxionbiotech.com/images/empresario/medium/');
        }
        $uriOffice="http://".$config['websites']['office']; 
        if(Zend_Registry::get('domain') == 'office') {
            defined('STATIC_OFFICE__IMG')
                || define('STATIC_OFFICE_IMG',$config['app']['imgOffice']);
            defined('CAPTCHA_FONT')
                || define('CAPTCHA_FONT',$config['captcha']['font']);
            defined('CAPTCHA_IMG')
                || define('CAPTCHA_IMG',$config['captcha']['img']);
            defined('CAPTCHA_URL')
                || define('CAPTCHA_URL',$uriOffice.'/captcha');
            defined('URL_FUXION')
                || define('URL_FUXION','http://test.fuxionbiotech.com/images/empresario/medium/');
        }
        $uriChallenge="http://".$config['websites']['challenge']; 
//        if(Zend_Registry::get('domain') == 'challenge') {
            defined('CHALLENGE_DINAMIC_URL')
                || define('CHALLENGE_DINAMIC_URL',$uriChallenge.'/dinamic/');
//        }
        
        $doctypeHelper = new Zend_View_Helper_Doctype();                
        $doctypeHelper->doctype(Zend_View_Helper_Doctype::XHTML1_STRICT);
        $v->headTitle($config['resources']['view']['title'])->setSeparator(' | ');
        $v->headMeta()->appendHttpEquiv('Content-Type',
            'text/html; charset=utf-8');
        $v->headMeta()->appendName("author", "onlineproduction");
        $v->headMeta()->setName("language", "es");
        $v->headMeta()->appendName("description",
            "managent aplication");
        $v->headMeta()->appendName("keywords",
            "ayuda.");
        if(APPLICATION_ENV!='LOCAL') $this->frontController->throwExceptions(false); 
    }
    
    public function getVersion(){
        $filename = APPLICATION_PATH.'/../last_commit';
        $version=date('dm');
        if(is_readable($filename)){
            $version=trim(file_get_contents($filename));
        }
        return $version;
    }
    
    public function _initRegistries() {        
        $this->bootstrap('multidb');
        $db = $this->getPluginResource('multidb')->getDb('db');
        Zend_Db_Table::setDefaultAdapter($db);
        //$multidb = $this->getPluginResource('multidb');
        Zend_Registry::set('multidb', $db);
        Zend_Registry::set('dbAdmin', $this->getPluginResource('multidb')->getDb('dbAdmin'));
        Zend_Registry::set('dbOV', $this->getPluginResource('multidb')->getDb('dbOV'));
        Zend_Registry::set('dbChallenge', $this->getPluginResource('multidb')->getDb('dbChallenge'));
       //Zend_Debug::dump($db); exit;
        
        /*
        $this->bootstrap('cachemanager');
         $cache = $this->getResource('cachemanager')->getCache('file');
         
         Zend_Registry::set('cache', $cache);       
         
         */  
//        $this->_executeResource('log');
//        $log = $this->getResource('log');
//        Zend_Registry::set('log', $log);
    }
    
    public function _initTranslate() {
        $config = Zend_Registry::get('config');
        
        $languages = $config['language'];
        $lang = $languages['default'];
        //var_dump($languages);
        
        $ip = 'php.net';
        $ip = '212.32.52.135'; //IP de ejemplo
        $ip = '190.12.71.44'; //IP de ejemplo Peru
        //$countryCode = geoip_country_code_by_name($ip);
        //var_dump(geoip_country_code_by_name($ip));
        
        /*foreach ($languages as $country => $ln) {
            if ($countryCode == $country) $lang = $ln;
        }*/
        
        //var_dump($lang);
        
        $translator = new Zend_Translate(
                Zend_Translate::AN_ARRAY,
                APPLICATION_PATH . '/configs/languages/',
                $lang,
                array('scan' => Zend_Translate::LOCALE_DIRECTORY)
        );
        
        /*$trans = new Zend_Translate(
                            Zend_Translate::AN_ARRAY,
                            APPLICATION_PATH . '/configs/languages/',
                            $lang);*/
        
        Zend_Registry::set('Zend_Translate', $translator);
        Zend_Validate_Abstract::setDefaultTranslator($translator);       
        
    }
    
    protected function _initUser() {
        $storage = new Zend_Auth_Storage_Session(Zend_Registry::get('domain'));
        Zend_Auth::getInstance()->setStorage($storage);
        
        $auth = Zend_Auth::getInstance();
        /*
            if ($auth->hasIdentity()) {
                if ($user = Users_Service_User::findOneByOpenId($auth->getIdentity())) {
                    $userLastAccess = strtotime($user->last_access);
                    //update the date of the last login time in 5 minutes
                    if ((time() - $userLastAccess) > 60*5) {
                        $date = new Zend_Date();
                        $user->last_access = $date->toString('YYYY-MM-dd HH:mm:ss');
                        $user->save();
                    }
                    Smapp::setCurrentUser($user);
                }
            }
            return Smapp::getCurrentUser();
         */
    }

    protected function _initAutoload() { 
        new Zend_Application_Module_Autoloader(array( 
            'namespace' => 'Application', 
            'basePath' => APPLICATION_PATH . '/models', 
        ));
        
        new Zend_Application_Module_Autoloader(array( 
            'namespace' => 'Businessman', 
            'basePath' => APPLICATION_PATH . '/entitys/businessman', 
        ));
        
        new Zend_Application_Module_Autoloader(array( 
            'namespace' => 'Shop', 
            'basePath' => APPLICATION_PATH . '/entitys/shop', 
        ));
        
        new Zend_Application_Module_Autoloader(array( 
            'namespace' => 'Mailing', 
            'basePath' => APPLICATION_PATH . '/entitys/mailing', 
        ));
        
        new Zend_Application_Module_Autoloader(array( 
            'namespace' => 'Admin', 
            'basePath' => APPLICATION_PATH . '/entitys/admin', 
        ));
        
        new Zend_Application_Module_Autoloader(array( 
            'namespace' => 'Challenge', 
            'basePath' => APPLICATION_PATH . '/entitys/challenge', 
        ));
        
        
        
        new Zend_Application_Module_Autoloader(array( 
            'namespace' => 'Biller', 
            'basePath' => APPLICATION_PATH . '/entitys/biller', 
        ));
    } 
    
//    protected function _initAcl() {
//        $cache = Zend_Registry::get('Cache');
//        if (!$acl = $cache->load('acl')) {
//            $acl = new Core_Acl();
//            $cache->save($acl, 'acl');
//        }
//        
//        Zend_Registry::set('Zend_Acl', $acl);
//    }
    
    
    public function _initRouter() {	
        $frontController = Zend_Controller_Front::getInstance();
        $rConfig = new Zend_Config_Ini(APPLICATION_PATH . '/configs/routes.ini', Zend_Registry::get('domain'));
        
        $router = $frontController->getRouter();
        $router->addConfig($rConfig,'routes');
    }
}

