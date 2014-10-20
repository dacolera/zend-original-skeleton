<?php

class App_Model_DAO_Sistema_Usuarios extends App_Model_DAO_Abstract
{
	protected static $instance = null;

	protected $_name = 'sis_usuarios';
	protected $_primary = 'usr_idUsuario';
	protected $_rowClass = 'App_Model_Entity_Sistema_Usuario';

	protected $_dependentTables = array(
		'App_Model_DAO_Galerias_Arquivos',
	);

	protected $_referenceMap = array(
		'Perfil' => array(
			self::COLUMNS => 'usr_idPerfil',
			self::REF_TABLE_CLASS => 'App_Model_DAO_Sistema_Perfis',
			self::REF_COLUMNS => 'per_idPerfil'
		)
	);

	/**
	 * Implementação do método Singleton para obter a instancia da classe
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