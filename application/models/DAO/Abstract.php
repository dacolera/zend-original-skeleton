<?php

abstract class App_Model_DAO_Abstract extends Zend_Db_Table_Abstract
{
	protected $_tablePrefix = 'jur';
	protected $_rowsetClass = 'App_Model_Collection';

	protected function _setupTableName()
	{
		if (isset($this->_tablePrefix) && strlen($this->_name)) {
			$this->_name = "{$this->_tablePrefix}_{$this->_name}";
		}
		parent::_setupTableName();
	}

	public function createRowset(array $data = null)
	{
		return new $this->_rowsetClass(array(
			'table' => $this,
			'rowClass' => $this->_rowClass,
			'readOnly' => false,
			'stored' => false,
			'data' => $data
		));
	}

	public function getCount(Zend_Db_Select $where)
	{
		$select = $this->getAdapter()->select()
			->from(array('temp' => $where->reset(Zend_Db_Select::LIMIT_COUNT)->reset(Zend_Db_Select::LIMIT_OFFSET)), 'COUNT(1)');
		return $this->getAdapter()->fetchOne($select);
	}
}