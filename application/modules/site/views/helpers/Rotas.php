<?php

class Zend_View_Helper_Rotas extends Zend_View_Helper_Abstract
{
	
	public function Rotas($modulo, $nomeUnidade)
	{
		if(in_array($modulo, App_Funcoes_Rotulos::$modulosDinamicos)){
			$request = Zend_Controller_Front::getInstance()->getRequest();
			
			$daoUnidades = App_Model_DAO_Unidades::getInstance();
			
			$listaNomes = $daoUnidades->getAdapter()->fetchAll(
				$daoUnidades->getAdapter()->select()
					->from($daoUnidades->info('name'), 'uni_nome')
			);
			
			$arrayUnidades = array();
			
			$urlBase = Zend_Registry::get('config')->paths->site->base;
			$urlAtual = $_SERVER['HTTP_REFERER'];
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
			return $urlFinal;
		} else {
			return false;
		}
	}
	
	private function removeBarra($urlFinal)
	{
		if(substr($urlFinal, strlen($urlFinal)-1) == '/') $urlFinal = substr($urlFinal, 0, strlen($urlFinal)-1);
		return $urlFinal;
	}
	
	private function getDadosSessao()
	{
		$sessaoUnidade = new Zend_Session_Namespace('unidade');
		return isset($sessaoUnidade->dados) ? $sessaoUnidade->dados : null;
	}
}
