<?php

class Zend_View_Helper_HasPermission extends Zend_View_Helper_Abstract
{
	public function HasPermission($module, $action)
	{
		//$usuario = App_Plugin_Login::getInstance()->getIdentity();
		//$found = false;
	
		/*foreach ($usuario->getPerfil()->getPermissoes() as $acao) {
			if ($acao->getNome() == $action && $acao->getModulo()->getNome() == $module) {
				$found = true;
				break;
			}
		}*/
		return true;//$found;
	}
}