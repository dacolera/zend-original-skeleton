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
					'path' => 'modules/site/plugins/'
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
		$configSistema = new Zend_Config(require_once APPLICATION_ROOT . '/configs/configuracao.php');
		Zend_Registry::set('config', $config);
		Zend_Registry::set('configInfo', $configSistema);
	}

	protected function _initFrontController()
	{
		$controller = Zend_Controller_Front::getInstance();
		$controller->setControllerDirectory(APPLICATION_PATH . '/controllers/')
			->registerPlugin(new Zend_Controller_Plugin_ErrorHandler())
			->registerPlugin(new App_Plugin_Url())			
			->throwExceptions(false);

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

		Zend_Layout::startMvc(array(
			'layout' => 'layout'
		));

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

	protected function _initRoute()
	{
		$router = Zend_Controller_Front::getInstance()->getRouter();

		// -------------------------------
		// P�ginas Din�micas
		// -------------------------------
		
		
		$router->addRoute(
			'projetos-especiais',
			new Zend_Controller_Router_Route(
				'projetos-especiais',
				array('controller' => 'index', 'action' => 'projetos-especiais')
			)
		);
		
		$router->addRoute(
			'servicos',
			new Zend_Controller_Router_Route(
				'servicos',
				array('controller' => 'servicos', 'action' => 'teleview')
			)
		);
		
		$router->addRoute(
			'fale-conosco',
			new Zend_Controller_Router_Route(
				'fale-conosco',
				array('controller' => 'index', 'action' => 'fale-conosco')
			)
		);
		
		$router->addRoute(
			'faq',
			new Zend_Controller_Router_Route(
				'faq',
				array('controller' => 'index', 'action' => 'faq')
			)
		);
		
		$router->addRoute(
			'residencial',
			new Zend_Controller_Router_Route(
				'solucoes',
				array('controller' => 'solucoes', 'action' => 'monitoramento-residencial')
			)
		);
		
		$router->addRoute(
			'sobre-empresa',
			new Zend_Controller_Router_Route(
				'institucional',
				array('controller' => 'institucional', 'action' => 'sobre-empresa')
			)
		);
	}			
}