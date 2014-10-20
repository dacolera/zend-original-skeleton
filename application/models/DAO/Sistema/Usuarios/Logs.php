<?php

class App_Model_DAO_Sistema_Usuarios_Logs extends App_Model_DAO_Abstract
{
	protected static $instance = null;

	protected $_name = 'sis_usuarios_logs';
	protected $_primary = 'usr_log_idLog';
	protected $_rowClass = 'App_Model_Entity_Sistema_Usuario_Log';

	protected $_referenceMap = array(
		'Usuario' => array(
			self::COLUMNS => 'usr_log_idUsuario',
			self::REF_TABLE_CLASS => 'App_Model_DAO_Sistema_Usuarios',
			self::REF_COLUMNS => 'usr_idUsuario'
		)
	);

	/**
	 * Implementação do método Singleton para obter a instancia da classe
	 *
	 * @return App_Model_DAO_Sistema_Usuarios_Logs
	 */
	public static function getInstance()
	{
		if (null === self::$instance) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}