<?php

class App_Plugin_Auth extends Zend_Controller_Plugin_Abstract
{
//controladores e a��es que n�o requer login
	private $semLogin = array(
		'index' => array('login', 'lost-password'),
		'sistema_galeria' => array('upload')
	);

	//controladores e a��es que n�o requer permiss�o
	private $semPermissao = array(
		'index' => array('index', 'keep-alive', 'access-denied', 'change-password'),
		'utils' => array('busca-endereco', 'resize-galeria'),
		'produtos_galeria' => array('index'),
		'sistema_galeria' => array('index', 'insert-galeria', 'update-galeria', 'delete-galeria', 'insert-arquivo', 'update-arquivo', 'delete-arquivo', 'upload'),
		'webmarketing_newsletters' => array('preview')
	);

	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		//a��es que n�o requerem login para serem executadas
		if (array_key_exists($request->getControllerName(), $this->semLogin)
			&& (in_array($request->getActionName(), $this->semLogin[$request->getControllerName()]))) {
			return true;
		}

		$login = App_Plugin_Login::getInstance();
		if ($login->hasIdentity()) {
			//a��es que n�o requer permiss�o para serem executadas
			if (array_key_exists($request->getControllerName(), $this->semPermissao)
				&& (in_array($request->getActionName(), $this->semPermissao[$request->getControllerName()]))) {
				return true;
			} else {
				//verifica a permissao
				$found = true;
				foreach ($login->getIdentity()->getPerfil()->getPermissoes() as $acao) {
					if ($acao->getNome() == $request->getActionName() && $acao->getModulo()->getNome() == $request->getControllerName()) {
						$found = true;
						break;
					}
				}
				if (false === $found) {
					$this->getResponse()->setHttpResponseCode(401)
						->setRedirect(Zend_Registry::get('config')->paths->admin->base.'/index/access-denied')
						->sendResponse();
				}
			}
		} else {
			$this->getResponse()->setHttpResponseCode(401)
				->setRedirect(Zend_Registry::get('config')->paths->admin->base .'/index/login')
				->sendResponse();
		}
	}
}