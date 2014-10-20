<?php
class App_Model_Entity_Sistema_Usuario_Preferencia extends App_Model_Entity_Abstract
{
	/**
	 * @var App_Model_Entity_Sistema_Usuario
	 */
	protected $objUsuario = null;

	public function __sleep()
	{
		$fields = array_merge(parent::__sleep(), array('objUsuario'));
		return $fields;
	}

	public function __wakeup()
	{
		parent::__wakeup();
		$this->setTable(App_Model_DAO_Sistema_Usuarios_Preferencias::getInstance());
	}

	public function save()
	{
		try {
			parent::save();
		} catch (Exception $e) {
			throw new Zend_Db_Table_Row_Exception($e->getMessage(), $e->getCode());
		}
	}	
	
	public function getCodigo()
	{
		return (int) $this->usr_pref_idPreferencia;
	}
	
	public function setUsuario(App_Model_Entity_Sistema_Usuario $value)
	{
		$this->objUsuario = $value;
		$this->usr_pref_idUsuario = $value->getCodigo();
		return $this;
	}
	
	public function getUsuario()
	{
		if (null === $this->objUsuario && $this->getCodigo()) {
			$this->objUsuario = $this->findParentRow(App_Model_DAO_Sistema_Usuarios::getInstance(), 'Usuario');
		}
		return $this->objUsuario;
	}

	public function setFiltros($value)
	{
		$this->usr_pref_filtros = (string) $value;
		return $this;
	}

	public function getFiltros()
	{
		return (string) $this->usr_pref_filtros;
	}

	public function setColunas($value)
	{
		$this->usr_pref_colunas = (string) $value;
		return $this;
	}

	public function getColunas()
	{
		return (string) $this->usr_pref_colunas;
	}
}