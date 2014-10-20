<?php

/**
 * ...
 *
 * @category 	Model
 * @package 	Model_Entity
 */
abstract class App_Model_Entity_Abstract extends Zend_Db_Table_Row_Abstract
{
	/**
	 * Valida a consistência dos dados
	 * 
	 * @param array $filters
	 * @param array $validators
	 * @param array $data
	 * @return boolean
	 * @throws App_Validate_Exception
	 */
	protected function validate(array $filters, array $validators, array $data)
	{
		$filter = new Zend_Filter_Input($filters, $validators, $data);
		if ($filter->isValid() === false)
		{
			$errors = array_merge($filter->getMissing(), $filter->getInvalid());
			$returnErrors = array();
			foreach ($errors as $field => $value)
			{
				$k = array_keys($value);
				$returnErrors[$field] = $value[$k[0]];
			}
			throw new App_Validate_Exception('Dados inconsistentes', null, $returnErrors);
		}
		return true;
	}
}