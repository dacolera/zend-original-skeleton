<?php

abstract class App_Funcoes_Money
{
	/**
	 * Converte formato de moeda para formado float
	 *
	 * @param string $valor
	 * @return float
	 */
	public static function toFloat($valor)
	{
		return str_replace(',', '.', str_replace('.', '', $valor));
	}

	/**
	 * Converte formato float para formado de moeda
	 *
	 * @param float $valor
	 * @return string
	 */
	public static function toCurrency($valor, $decimal=2)
	{
		return number_format($valor, $decimal, ',', '.');
	}
}