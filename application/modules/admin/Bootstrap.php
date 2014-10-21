<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
	protected function _initAutoload()
	{
		$loader = new Zend_Loader_Autoloader_Resource(array(
			'namespace' => 'App',
			'basePath'  => APPLICATION_ROOT,
			'resourceTypes' => array(
				'models' => array(
					'namespace' => 'Model',
					'path' => 'models/'
				),
				'plugins' => array(
					'namespace' => 'Plugin',
					'path' => 'modules/admin/plugins/'
				),
				'filters' => array(
					'namespace' => 'Filter',
					'path' => 'library/Filters/'
				),
				'validators' => array(
					'namespace' => 'Validate',
					'path' => 'library/Validators/'
				),
				'functions' => array(
					'namespace' => 'Funcoes',
					'path' => 'library/Funcoes/'
				),
			)
		));
		return $loader;
	}

	protected function _initRegistry()
	{
		$config = new Zend_Config(require_once APPLICATION_ROOT . '/configs/config.php');
		//$configSistema = new Zend_Config(require_once APPLICATION_ROOT . '/configs/configuracao.php');
		Zend_Registry::set('config', $config);
		//Zend_Registry::set('configInfo', $configSistema);
	}

	protected function _initFrontController()
	{
		$controller = Zend_Controller_Front::getInstance();
		$controller->setControllerDirectory(APPLICATION_PATH . '/controllers/')
			->registerPlugin(new App_Plugin_Auth())
			->throwExceptions(true);

		return $controller;
	}

	protected function _initView()
	{
		$view = new Zend_View();
		$view->doctype('XHTML1_STRICT');
		$view->setEncoding('ISO-8859-1');
		$view->setEscape('htmlentities');
		$view->HeadTitle(Zend_Registry::get('config')->project)->setSeparator(' - ');

		$viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer($view);
		Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);

		return $view;
	}

	/**
	 * Faz o cacheamento dos metadados de Zend_Db_Table_Abstract
	 * 
	 * @return Zend_Cache
	 */
	protected function _initMetadataCache()
	{
		if ('development' != APPLICATION_ENV) {
			$frontendOptions = array(
				'lifetime' => 86400, // 24 horas
				'automatic_serialization' => true
			);
			$backendOptions = array(
				'cache_dir' => APPLICATION_ROOT . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'metadata'
			);
	
			$cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
			Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
	
			return $cache;
		}
	}
}