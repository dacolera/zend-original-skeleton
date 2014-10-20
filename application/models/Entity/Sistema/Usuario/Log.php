<?php
class App_Model_Entity_Sistema_Usuario_Log extends App_Model_Entity_Abstract
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
		$this->setTable(App_Model_DAO_Sistema_Usuarios_Logs::getInstance());
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
		return (int) $this->usr_log_idLog;
	}
	
	public function setUsuario(App_Model_Entity_Sistema_Usuario $value)
	{
		$this->objUsuario = $value;
		$this->usr_log_idUsuario = $value->getCodigo();
		return $this;
	}
	
	public function getUsuario()
	{
		if (null === $this->objUsuario && $this->getCodigo()) {
			$this->objUsuario = $this->findParentRow(App_Model_DAO_Sistema_Usuarios::getInstance(), 'Usuario');
		}
		return $this->objUsuario;
	}

	public function setModulo($value)
	{
		$this->usr_log_modulo = (string) $value;
		return $this;
	}

	public function getModulo()
	{
		return (string) $this->usr_log_modulo;
	}

	public function setAcao($value)
	{
		$this->usr_log_acao = (string) $value;
		return $this;
	}

	public function getAcao()
	{
		return (string) $this->usr_log_acao;
	}

	public function setData($value)
	{
		$this->usr_log_data = (string) $value;
		return $this;
	}

	public function getData()
	{
		return (string) $this->usr_log_data;
	}
	
	public function setParametros($value)
	{
		$this->usr_log_parametros = (string) $value;
		return $this;
	}

	public function getParametros()
	{
		return (string) $this->usr_log_parametros;
	}
}