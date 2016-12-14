<?php
/**
* @file Bootstrap.php
* @synopsis  Bootstrap
* @author Yee, <rlk002@gmail.com>
* @version 1.0
* @date 2016-07-12 18:59:47
*/

require_once 'App/Bootstrap/Abstract.php';

class Bootstrap extends App_Bootstrap_Abstract
{
	protected $_first = [
			'Autoloader',
			'Environment'
		];

	protected $_last  = [
			'AppPaths'
		];

	protected function _initAutoloader()
	{
		require_once 'Zend/Loader/Autoloader.php';
		$loader = Zend_Loader_Autoloader::getInstance();
		$loader->registerNamespace('App_');
		$loader->registerNamespace('Zend_');
		$loader->setFallbackAutoloader(TRUE);
	}

	protected function _initEnvironment()
	{
		$file = APPLICATION_PATH . '/configs/environment.php';
		if (!is_readable($file)){
			throw new Zend_Exception('Cannot find the environment.php file!');
		}

		require_once ($file);
		if (!defined('APPLICATION_ENV')){
			throw new Zend_Exception('The APPLICATION_ENV constant is not defined in ' . $file);
		}

		Zend_Registry::set('IS_PRODUCTION', APPLICATION_ENV == APP_STATE_PRODUCTION);
		Zend_Registry::set('IS_DEVELOPMENT', APPLICATION_ENV == APP_STATE_DEVELOPMENT);
		Zend_Registry::set('IS_STAGING', APPLICATION_ENV == APP_STATE_STAGING);
	}

	protected function _initAppVersion()
	{
		$configuration = App_DI_Container::get('ConfigObject');
		if (isset($configuration->release->version)){
			define('APP_VERSION', $configuration->release->version);
		}else{
			define('APP_VERSION', 'unknown');
		}
		Zend_Registry::set('APP_VERSION', APP_VERSION);
	}

	protected function _initAppPaths()
	{   
		$paths = [
				APPLICATION_PATH,
				get_include_path() ,
			];
		set_include_path(implode(PATH_SEPARATOR, $paths));
	}  

	protected function _initDb()
	{
		$config = App_DI_Container::get('ConfigObject');

		$dbAdapter = Zend_Db::factory($config->resources->db);
		Zend_Db_Table_Abstract::setDefaultAdapter($dbAdapter);
		Zend_Registry::set('dbAdapter', $dbAdapter);

		Zend_Db_Table_Abstract::setDefaultMetadataCache(App_DI_Container::get('CacheManager')->getCache('default'));
	}

	protected function _initActionHelpers()
	{
		Zend_Controller_Action_HelperBroker::addHelper(new App_Controller_Action_Helper_Logger());
	}

	protected function _initPlugins()
	{
		$frontController = Zend_Controller_Front::getInstance();
		$frontController->registerPlugin(new App_Plugin_VersionHeader());
	}

	protected function _initSession()
	{
		Zend_Session::start();
	}

	public function runApp()
	{
		$front = Zend_Controller_Front::getInstance();
		$front->setControllerDirectory('controllers');
		$front->addModuleDirectory(APPLICATION_PATH);
		$front->setParam('useDefaultControllerAlways', true);
		$front->setParam('noViewRenderer', true);
		$front->dispatch();
	}
}
