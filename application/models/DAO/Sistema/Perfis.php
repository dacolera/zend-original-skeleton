<?php

class App_Model_DAO_Sistema_Perfis extends App_Model_DAO_Abstract
{
	protected static $instance = null;

	protected $_name = 'sis_perfis';
	protected $_primary = 'per_idPerfil';
	protected $_rowClass = 'App_Model_Entity_Sistema_Perfil';

	protected $_dependentTables = array(
		'App_Model_DAO_Sistema_Usuarios',
		'App_Model_DAO_Sistema_Permissoes'
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