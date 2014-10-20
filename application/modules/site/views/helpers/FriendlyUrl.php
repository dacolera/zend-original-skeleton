<?php

class Zend_View_Helper_FriendlyUrl extends Zend_View_Helper_Abstract
{
	public function FriendlyUrl()
	{
		$FriendlyUrl = '';
		foreach(func_get_args() as $param)
		{
			$FriendlyUrl .= App_Funcoes_SEO::toString($param) . '/';
		}
		return Zend_Registry::get('config')->paths->site->base .'/'. substr($FriendlyUrl, 0, -1);
	}

	private function normalizestr($str)
	{
		$str = htmlentities($str);
		$str = preg_replace('/&((?i)[a-z]{1,2})(?:grave|accent|acute|circ|tilde|uml|ring|lig|cedil|slash);/', '$1', $str);
		$str = str_replace(array('&ETH;', '&eth;', '&THORN;', '&thorn;'), array('dh', 'd', 'TH', 'th'), $str);
		return $str;
	}
}