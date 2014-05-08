<?php


class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    public function _initView()
    {
        $this->bootstrap('layout');        
        $layout = $this->getResource('layout');
        $v = $layout->getView();

        $v->addHelperPath('Core/View/Helper', 'Core_View_Helper');
        $config = Zend_Registry::get('config');
        
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
        defined('STATIC_ADMIN_IMG')
            || define('STATIC_ADMIN_IMG',$config['app']['imgAdmin']);
        defined('ROOT_IMG_DINAMIC')
            || define('ROOT_IMG_DINAMIC',$config['app']['rootImgDinamic']);
                
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
//        $v->headLink()->appendStylesheet($v->S('/css/estilo.css'));
//        $v->headLink()->appendStylesheet($v->S(
//                '/css/ie7.css', 'screen', 'lt IE 8'
//            )
//        );
//        $v->headLink(array(
//            'rel' => 'shortcut icon', 'href' => $v->S('/imagenes/favicon.ico')
//            )
//        );        
      //  $js = sprintf("var urls = {siteUrl : '%s'}", $config['app']['siteUrl']);
      //  $v->headScript()->appendScript($js);
//        $v->headLink()->appendAlternate($v->S('/humans.txt'), 'text/plain',
//            'author', '');        
    }

    public function _initRegistries()
    {        
        //$config = Zend_Registry::get('config');
       /// Zend_Debug::dump($config); exit;
        
//        $this->_executeResource('cachemanager');        
//        $cacheManager = $this->getResource('cachemanager');
//        Zend_Debug::dump($cacheManager); exit;
//        $a = $cacheManager->getCache($config['app']['cache']);
//        Zend_Debug::dump($a); exit;
//        Zend_Registry::set('cache',
//            $cacheManager->getCache(
//                $config['app']['cache']
//            )
//        );
        $this->bootstrap('multidb');
        $db = $this->getPluginResource('multidb')->getDb('db');
        Zend_Db_Table::setDefaultAdapter($db);
        //$multidb = $this->getPluginResource('multidb');
        Zend_Registry::set('multidb', $db);
       //Zend_Debug::dump($db); exit;

//        $this->_executeResource('log');
//        $log = $this->getResource('log');
//        Zend_Registry::set('log', $log);
    }

    public function _initActionHelpers()
    {
//        Zend_Controller_Action_HelperBroker::addHelper(
//            new Core_Controller_Action_Helper_Auth()
//        );
//        Zend_Controller_Action_HelperBroker::addHelper(
//            new App_Controller_Action_Helper_Security()
//        );
        Zend_Controller_Action_HelperBroker::addHelper(
            new Core_Controller_Action_Helper_Mail()
        );
        
    }

    
    public function _initTranslate()
    {
        $config = Zend_Registry::get('config');
        
        $languages = $config['language'];
        $lang = $languages['default'];
        //var_dump($languages);
        
        $ip = 'php.net';
        $ip = '212.32.52.135'; //IP de ejemplo
        $ip = '190.12.71.44'; //IP de ejemplo Peru
        $countryCode = geoip_country_code_by_name($ip);
        //var_dump(geoip_country_code_by_name($ip));
        
        foreach ($languages as $country => $ln) {
            if ($countryCode == $country) $lang = $ln;
        }
        
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

//    protected function _initZFDebug()
//    {
//        if ('local' == APPLICATION_ENV) {
//            $options = array('plugins' => array(
//                    'Variables',
//                    'File' => array('base_path' => APPLICATION_PATH),
//                    'Memory',
//                    'Time',
//                    'Registry',
//                    'Exception'
//                ));
//
//            if ($this->hasPluginResource('multidb')) {
//                $this->bootstrap('multidb');
//                $db = $this->bootstrap('multidb')->
//                        getResource('multidb')->getDb('globalhumanitariaperu');
//                $options['plugins']['Database']['adapter'] = $db;
//            }
//
//            if ($this->hasPluginResource('cache')) {
//                $this->bootstrap('cache');
//                $cache = $this - getPluginResource('cache')->getDbAdapter();
//                $options['plugins']['Cache']['backend'] = $cache->getBackend();
//            }
//
//            $debug = new ZFDebug_Controller_Plugin_Debug($options);
//
//            $this->bootstrap('frontController');
//            $frontController = $this->getResource('frontController');
//            $frontController->registerPlugin($debug);
//        }
//    }

}

