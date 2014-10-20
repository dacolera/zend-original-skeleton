<?php

class App_Model_DAO_Sistema_Usuarios_Preferencias extends App_Model_DAO_Abstract
{
	protected static $instance = null;

	protected $_name = 'sis_usuarios_preferencias';
	protected $_primary = 'usr_pref_idPreferencia';
	protected $_rowClass = 'App_Model_Entity_Sistema_Usuario_Preferencia';

	protected $_referenceMap = array(
		'Usuario' => array(
			self::COLUMNS => 'usr_pref_idUsuario',
			self::REF_TABLE_CLASS => 'App_Model_DAO_Sistema_Usuarios',
			self::REF_COLUMNS => 'usr_idUsuario'
		)
	);

	/**
	 * Implementação do método Singleton para obter a instancia da classe
	 *
	 * @return App_Model_DAO_Sistema_Usuarios_Preferencias
	 */
	public static function getInstance()
	{
		if (null === self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}