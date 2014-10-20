<?php

class App_Model_Collection extends Zend_Db_Table_Rowset_Abstract
{
	const ON_BEFORE_ADD = 'beforeAddItem';
	const ON_AFTER_ADD = 'afterAddItem';
	const ON_BEFORE_REMOVE = 'beforeRemoveItem';
	const ON_AFTER_REMOVE = 'afterRemoveItem';

	protected $events = array();

	public function __sleep()
	{
		return array_merge(parent::__sleep(), array('events'));
	}

	public function offsetSet($offset, $value)
	{
		if ($this->_readOnly !== true) {
			/*if (!is_object($value) || get_class($value) != $this->getTable()->getRowClass()) {
				throw new Zend_Db_Table_Rowset_Exception("This collection only accepts objects of type: {$this->getTable()->getRowClass()}");
			}*/

			if (false !== $this->fireEvent(self::ON_BEFORE_ADD, array($value))) {
				$this->_data[(int) $offset] = $value->toArray();
				$this->_rows[(int) $offset] = $value;
				$this->_count = count($this->_data);

				$this->fireEvent(self::ON_AFTER_ADD, array($value));
				return $this->getRow($offset);
			}
		} else {
			throw new Zend_Db_Table_Rowset_Exception("This collection is read-only");
		}
	}

	public function offsetAdd($value)
	{
		return $this->offsetSet($this->_count, $value);
	}

	public function offsetGet($offset)
	{
		if (isset($this->_data[(int) $offset])) {
			return $this->getRow((int) $offset);
		} else {
			throw new Zend_Db_Table_Rowset_Exception("Undefined index {$offset}");
		}
	}
	
	public function offsetUnset($offset)
	{
		$offset = (int) $offset;
		if (isset($this->_data[(int)$offset])) {
			$objItem = $this->getRow($offset);
			if (false !== $this->fireEvent(self::ON_BEFORE_REMOVE, array($objItem))) {
				if (isset($this->_data[$offset])) {
					unset($this->_data[$offset]);
		
					//refaz os índices dos itens
					$dataItems = array();
					foreach ($this->_data as $tempItem) {
						$dataItems[] = $tempItem;
					}
					$this->_data = $dataItems;
					unset($dataItems);
				}
		
				if (isset($this->_rows[$offset])) {
					unset($this->_rows[$offset]);
		
					//refaz os índices dos itens
					$rowsItems = array();
					foreach ($this->_rows as $tempItem) {
						$rowsItems[] = $tempItem;
					}
					$this->_rows = $rowsItems;
					unset($rowsItems);
				}
		
				$this->_count = count($this->_data);
	
				$this->fireEvent(self::ON_AFTER_REMOVE, array($objItem));
			}
		}
	}

	public function offsetRemoveAll()
	{
		while ($this->count() > 0) {
			$this->offsetUnset(0);
		}
	}

	public function key()
	{
		return ($this->_pointer < $this->_count-1 ? $this->_pointer : $this->_count-1);
	}

	public function find($property, $value)
	{
		$return  = null;
		foreach ($this->_data as $offset => $item) {
			if (isset($item[$property]) && $item[$property] == $value) {
				$return = $this->offsetGet($offset);
				break;
			}
		}
		return $return;
	}
	
	public function findIndex($property, $value)
	{
		$return  = null;
		foreach ($this->_data as $offset => $item) {
			if (isset($item[$property]) && $item[$property] == $value) {
				$return = $offset;
				break;
			}
		}
		return $return;
	}

	public function createRow(array $data = array(), $defaultSource = null)
	{
		$row = $this->getTable()->createRow($data, $defaultSource);
		return $this->offsetAdd($row);
	}

	/**
	 * 
	 * @param string $event
	 * @param string $function
	 * @param object $scope
	 * @return App_Model_Listener
	 */
	public function on($event, $function, &$scope = null)
	{
		$this->events[$event][] = array(
			'fn' => $function,
			'scope' => $scope
		);
		return $this;
	}

	/**
	 * 
	 * @param string $event
	 * @param array $params
	 * @return mixed
	 */
	protected function fireEvent($event, array $params = null)
	{
		$return = null;
		if (isset($this->events[$event]))
		{
			foreach ($this->events[$event] as $listener)
			{
				$call = $listener['scope'] ? array($listener['scope'], $listener['fn']) : $listener['fn'];
				$return = (null !== $params) ? call_user_func_array($call, $params) : call_user_func($call);
			}
		}
		return $return;
	}
}