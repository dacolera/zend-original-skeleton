<?php

class App_Plugin_Url extends Zend_Controller_Plugin_Abstract
{
	private function getDadosSessao()
	{
		$sessaoUnidade = new Zend_Session_Namespace('unidade');
		return isset($sessaoUnidade->dados) ? $sessaoUnidade->dados : null;
	}

	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		if($request->getParam('clean', false) != false) {
			$sessaoUnidade = new Zend_Session_Namespace('unidade');
			$sessaoUnidade->dados = null;
			
			$this->getResponse()->setRedirect(Zend_Registry::get('config')->paths->site->base)->sendResponse();
			
		} else if($this->getDadosSessao() && in_array($request->getControllerName(), App_Funcoes_Rotulos::$modulosDinamicos)) {
			$daoUnidades = App_Model_DAO_Unidades::getInstance();
			
			if(($request->getParam('unidade', false) != false && !isset($_SERVER['HTTP_REFERER'])) || ($request->getParam('unidade', false) != false && $request->getControllerName() == 'sports-app')) {
				$unidade = $daoUnidades->fetchRow(
					$daoUnidades->select()->from($daoUnidades)->where('uni_url = ?', $request->getParam('unidade'))
				);
				
				if($unidade != null) {
					$sessaoUnidade = new Zend_Session_Namespace('unidade');
					$sessaoUnidade->dados = $unidade;
				}
				return;
			}
			
			$listaNomes = $daoUnidades->getAdapter()->fetchAll(
				$daoUnidades->getAdapter()->select()
					->from($daoUnidades->info('name'), 'uni_nome')
			);
			
			$arrayUnidades = array();
			
			$urlBase = Zend_Registry::get('config')->paths->site->base;
			$urlAtual = $request->getScheme() . '://' . $request->getHttpHost() . $request->getRequestUri();
			$urlAux = explode($urlBase, $urlAtual);
			$urlFinal = $urlBase;
			$nomeUnidade = App_Funcoes_SEO::toString($this->getDadosSessao()->getNome());
			
			if(!strrpos($urlAtual, $nomeUnidade)) $urlFinal .= "/{$nomeUnidade}"; 
			
			if($this->removeBarra($urlFinal) != $this->removeBarra($urlAtual) && isset($urlAux[1])) {
				$urlFinal .= $urlAux[1]; 
			}
			
			foreach($listaNomes as $nome) {
				$nomeTratado = App_Funcoes_SEO::toString($nome['uni_nome']);
				if($nomeTratado != $nomeUnidade && strpos($urlFinal, $nomeTratado)) {
					$pos = strpos($urlFinal, $nomeTratado);
					$urlFinal = substr($urlFinal, 0, $pos) . substr($urlFinal, $pos + strlen($nomeTratado)+1);
				}
			}
			
			if($this->removeBarra($urlFinal) != $this->removeBarra($urlAtual)) {
				$this->getResponse()->setRedirect($urlFinal)->sendResponse();
			}

		} else if($request->getParam('unidade', false) != false) {
			$daoUnidades = App_Model_DAO_Unidades::getInstance();
			$unidade = $daoUnidades->fetchRow(
				$daoUnidades->select()->from($daoUnidades)->where('uni_url = ?', $request->getParam('unidade'))
			);
			
			if($unidade != null) {
				$sessaoUnidade = new Zend_Session_Namespace('unidade');
				$sessaoUnidade->dados = $unidade;
			}
		}
	}
	
	private function removeBarra($urlFinal)
	{
		if(substr($urlFinal, strlen($urlFinal)-1) == '/') $urlFinal = substr($urlFinal, 0, strlen($urlFinal)-1);
		return $urlFinal;
	}
}