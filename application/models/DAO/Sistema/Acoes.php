<?php

class App_Model_DAO_Sistema_Acoes extends App_Model_DAO_Abstract
{
	protected static $instance = null;

	protected $_name = 'sis_modulos_acoes';
	protected $_primary = 'mod_acao_idAcao';
	protected $_rowClass = 'App_Model_Entity_Sistema_Acao';

	protected $_dependentTables = array(
		'App_Model_DAO_Sistema_Permissoes'
	);

	protected $_referenceMap = array(
		'Modulo' => array(
			self::COLUMNS => 'mod_acao_idModulo',
			self::REF_TABLE_CLASS => 'App_Model_DAO_Sistema_Modulos',
			self::REF_COLUMNS => 'mod_idModulo'
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