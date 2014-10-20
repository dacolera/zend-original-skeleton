<?php

abstract class App_Funcoes_Ext
{
	public static function fieldErrors(App_Validate_Exception $e)
	{
		$retorno = array();
		foreach ($e->getFields() as $fName => $fValue)
		{
			$retorno[] = array('id' => $fName, 'msg' => $fValue);
		}
		return $retorno;
	}

	public static function FilterSQL(Zend_Db_Select &$filter, $fils, $bindFields=null)
	{
		$comparison = array('gt' => '>', 'lt' => '<', 'eq' => '=');

		if (is_array($fils))
		{
			foreach ($fils as $fil)
			{
				if (is_array($bindFields) && in_array($fil['field'], array_keys($bindFields)))
				{
					$fil['field'] = $bindFields[$fil['field']];
				}
				switch ($fil['data']['type'])
				{
					case 'string':
						$valor = $fil['data']['value'];
						if (preg_match('/./u', $valor)) $valor = utf8_decode($valor);
						$filter->where("{$fil['field']} LIKE ?", "%{$valor}%");
						break;
					case 'numeric':
						$filter->where("{$fil['field']} {$comparison[$fil['data']['comparison']]} ?", $fil['data']['value']);
						break;
					case 'date':
						$data = substr($fil['data']['value'], -4) .'-'. substr($fil['data']['value'], 0, 2) .'-'. substr($fil['data']['value'], 3, 2);
						$filter->where("LEFT({$fil['field']}, 10) {$comparison[$fil['data']['comparison']]} ?", $data);
						break;
					case 'list':
						$list = implode("','", explode(',', $fil['data']['value']));
						$filter->where("{$fil['field']} IN('{$list}')");
						break;
					case 'set':
						$items = explode(',', $fil['data']['value']);
						$sql_aux = array();
						foreach ($items as $item)
						{
							$sql_aux[] = "FIND_IN_SET('{$item}', {$fil['field']})";
						}
						$filter->where("(". implode(' AND ', $sql_aux) .")");
						break;
					case 'boolean':
						$filter->where("{$fil['field']} = ?", (int)$fil['data']['value']);
						break;
				}
			}
		}
	}
}