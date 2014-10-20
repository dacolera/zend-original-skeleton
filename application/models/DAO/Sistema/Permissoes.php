<?php

class App_Model_DAO_Sistema_Permissoes extends App_Model_DAO_Abstract
{
	protected static $instance = null;

	protected $_name = 'sis_permissoes';
	protected $_primary = array('perm_idPerfil', 'perm_idAcao');
	protected $_sequence = false;

	protected $_referenceMap = array(
		'Perfil' => array(
			self::COLUMNS => 'perm_idPerfil',
			self::REF_TABLE_CLASS => 'App_Model_DAO_Sistema_Perfis',
			self::REF_COLUMNS => 'per_idPerfil'
		),
		'Acao' => array(
			self::COLUMNS => 'perm_idAcao',
			self::REF_TABLE_CLASS => 'App_Model_DAO_Sistema_Acoes',
			self::REF_COLUMNS => 'mod_acao_idAcao'
		),
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