<?php

class App_Funcoes_Array2xml
{
	public static function Array2xml($array) {
		$XML= new XMLWriter();
		$XML->openMemory();
		//$XML->startDocument('1.0','UTF-8');
		App_Funcoes_Array2xml::recursivo($XML,$array);
		$XML->endDocument();
		return utf8_encode($XML->outputMemory());
	}
	
	public static function recursivo($xml, $array)
	{	
		foreach ($array as $key => $value) {
			$element = explode("-", $key);
			if(is_array($value)) {
				$xml->startElement($element[0]);                                  
				App_Funcoes_Array2xml::recursivo($xml, $value);
			} else {
				if($value != null) $xml->writeElement($element[0], $value);
			}
		}
		$xml->endElement();
	}
}