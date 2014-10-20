<?php

class App_Model_DAO_Sistema_Configuracoes extends App_Model_DAO_Abstract
{
	protected static $instance = null;

	protected $_name = 'configuracoes';
	protected $_primary = 'conf_idConfiguracao';
	protected $_rowClass = 'App_Model_Entity_Sistema_Configuracao';
	
	/**
	 * Implementaчуo do mщtodo Singleton para obter a instancia da classe
	 *
	 * @return App_Model_DAO_Sistema_Configuracoes_Gerais
	 */
	public static function getInstance()
	{
		if (null === self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}