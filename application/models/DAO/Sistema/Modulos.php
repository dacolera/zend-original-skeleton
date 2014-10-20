<?php

class App_Model_DAO_Sistema_Modulos extends App_Model_DAO_Abstract
{
	protected static $instance = null;

	protected $_name = 'sis_modulos';
	protected $_primary = 'mod_idModulo';
	protected $_rowClass = 'App_Model_Entity_Sistema_Modulo';

	protected $_dependentTables = array(
		'App_Model_DAO_Sistema_Acoes'
	);

	/**
	 * Implementaчуo do mщtodo Singleton para obter a instancia da classe
	 * 
	 * @return App_Model_DAO_Galerias
	 */
	public static function getInstance()
	{
		if (null === self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}