<?php

class App_Validate_Cliente_Email extends Zend_Validate_Abstract
{
	const NOT_RECOGNIZED = 'notRecognized';

	/**
	 * @var App_Model_Entity_Cliente
	 */
	protected $cliente;

	protected $_messageTemplates = array(
		self::NOT_RECOGNIZED => "Este email já está cadastrado nessa unidade"
	);

	public function __construct(App_Model_Entity_Cliente $cliente)
	{
		$this->cliente = $cliente;
	}

	public function isValid($value)
	{
		$daoClientes = App_Model_DAO_Clientes::getInstance();
		$cliente = $daoClientes->fetchRow(
			$daoClientes->select()->where('cli_email = ?', $value)->where('cli_idUnidade = ?', $this->cliente->getUnidade()->getCodigo())
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