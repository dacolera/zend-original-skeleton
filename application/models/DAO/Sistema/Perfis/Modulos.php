<?php

class App_Model_DAO_Sistema_Perfis_Modulos extends App_Model_DAO_Abstract
{
	protected static $instance = null;

	protected $_name = 'sis_perfis_modulos';
	protected $_primary = array('per_mod_idModulo', 'per_mod_idPerfil');

	protected $_referenceMap = array(
		'Perfil' => array(
			self::COLUMNS => 'per_mod_idPerfil',
			self::REF_TABLE_CLASS => 'App_Model_DAO_Sistema_Perfis',
			self::REF_COLUMNS => 'per_idPerfil'
		),
		'Modulo' => array(
			self::COLUMNS => 'per_mod_idModulo',
			self::REF_TABLE_CLASS => 'App_Model_DAO_Sistema_Modulos',
			self::REF_COLUMNS => 'mod_idModulo'
		)
	);

	/**
	 * Implementação do método Singleton para obter a instancia da classe
	 *
	 * @return App_Model_DAO_Sistema_Perfis_Modulos
	 */
	public static function getInstance()
	{
		if (null === self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
}