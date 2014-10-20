<?php

class App_Validate_Exception extends Zend_Db_Table_Row_Exception
{
	protected $fields = array();

	public function __construct($message, $code = null, array $fields = null)
	{
		parent::__construct($message, $code);
		if ($fields !== null)
		{
			$this->fields = $fields;
		}
	}

	public function setField($name, $value)
	{
		$this->fields[$name] = $value;
	}

	public function getFields()
	{
		return $this->fields;
	}
}