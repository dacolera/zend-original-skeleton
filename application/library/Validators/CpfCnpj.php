<?php

class App_Validate_CpfCnpj extends Zend_Validate_Abstract
{
	const NOT_RECOGNIZED = 'notRecognized';
	const INVALID_CPF = 'invalidCPF';
	const INVALID_CNPJ = 'InvalidCNPJ';

	protected $_messageTemplates = array(
		self::NOT_RECOGNIZED => "'%value%' n‹o parece ser um CPF ou CNPJ v‡lido",
		self::INVALID_CPF => "CPF '%value%' inv‡lido",
		self::INVALID_CNPJ => "CNPJ '%value%' inv‡lido"
	);

	public function isValid($value)
	{
		$this->_setValue($value);
		$value = @ereg_replace('[^0-9]', '', $value);
		switch (strlen($value)) {
			case 11: // CPF
				if ($this->cpf($value) === false) {
					$this->_error(self::INVALID_CPF);
					return false;
				}
				break;
			case 14: // CNPJ
				if ($this->cnpj($value) === false) {
					$this->_error(self::INVALID_CNPJ);
					return false;
				}
				break;
			default: $this->_error(self::NOT_RECOGNIZED); return false;
		}
		return true;
	}

	protected function cpf($value)
	{
		$igual = true;
		for ($i=1, $t=strlen($value); $i<$t; $i++) {
			if ($value[$i-1] != $value[$i]) {
				$igual = false;
				break;
			}
		}
		if ($igual == true) return false;

		$primeiro_dv = $segundo_dv = 0;
		for ($i=0; $i<9; $i++) $primeiro_dv += $value[$i] * (10-$i);
		for ($i=0; $i<10; $i++) $segundo_dv += $value[$i] * (11-$i);

		//calcula o primeiro dígito verificador
		$x = $primeiro_dv % 11;
		if ((($x > 1) ? (11-$x) : 0) != $value[9]) return false;

		//calcula o segundo dígito verificador
		$x = $segundo_dv % 11;
		if ((($x > 1) ? (11-$x) : 0) != $value[10]) return false;

		return true;
	}

	protected function cnpj($value)
	{
		//calcula o primeiro dígito verificador
		$a = $i = $d1 = $d2 = 0;
		$j = 5;
		for ($i=0; $i<12; $i++) {
			$a += $value[$i] * $j;
			($j > 2) ? $j-- : $j = 9;
		}
		$a = $a % 11;
		($a > 1) ? $d1 = 11 - $a : $d1 = 0;

		//calcula o segundo dígito verificador
		$a = $i = 0;
		$j = 6;
		for ($i=0; $i<13; $i++) {
			$a += $value[$i] * $j;
			($j > 2) ? $j-- : $j = 9;
		}
		$a = ($a % 11);
		($a > 1) ? $d2 = 11 - $a : $d2 = 0;

		return (($d1 == $value[12]*1) && ($d2 == $value[13]*1));
	}
}