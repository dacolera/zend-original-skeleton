<?php

class Zend_View_Helper_Php2ExtFilter extends Zend_View_Helper_Abstract
{
	public function Php2ExtFilter($list)
	{
		$itens = array();
		foreach ($list as $key => $value) {
			$itens[] = "{$key}','{$value}";
		}
		return "['". implode("'],['", $itens) ."']";
	}
}