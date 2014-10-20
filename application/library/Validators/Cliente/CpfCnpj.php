<?php

class App_Validate_Cliente_CpfCnpj extends Zend_Validate_Abstract
{
	const NOT_RECOGNIZED = 'notRecognized';

	/**
	 * @var App_Model_Entity_Cliente
	 */
	protected $cliente;

	protected $_messageTemplates = array(
		self::NOT_RECOGNIZED => "Cliente já está cadastrado"
	);

	public function __construct(App_Model_Entity_Cliente $cliente)
	{
		$this->cliente = $cliente;
	}

	public function isValid($value)
	{
		$daoClientes = App_Model_DAO_Clientes::getInstance();
		$cliente = $daoClientes->fetchRow(
			$daoClientes->select()->where('cli_cpfcnpj = ?', $value)
		);
		if (null != $cliente) {
			if ($this->cliente->getCodigo() == $cliente->getCodigo()) {
				return true;
			} else {
				$this->_error(self::NOT_RECOGNIZED);
				return false;
			}
		} else {
			return true;
		}
	}
}