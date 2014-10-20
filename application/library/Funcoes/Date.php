<?php

class App_Funcoes_Date
{
	/**
	 * Conversor o formato da data de dd/mm/aaaa para aaaa/mm/dd e vice-versa
	 *
	 * @param string $date dd/mm/aaaa | aaaa-mm-dd [yy:m:ss]
	 * @return string
	 */
	public static function conversion($date, $boolTime = true)
	{
		if (strlen($date)) {
			$time = null;
			if (strlen($date) > 10) {
				$time = substr($date, 10);
				$date = substr($date, 0, 10);
			}

			$token = strpos($date, '/') ? '/' : '-';
			$tmp = explode($token, $date);
			foreach ($tmp as &$val) {
				$val = str_pad($val, 2, 0, STR_PAD_LEFT);
			}

			
			return $boolTime ? trim(implode(($token == '-' ? '/' : '-'), array_reverse($tmp)) ." {$time}") : trim(implode(($token == '-' ? '/' : '-'), array_reverse($tmp)) );
		}
	}

	/**
	 * Retorna a diferença entre datas
	 *
	 * @param date $data1 Menor data no formato americano Y-m-d
	 * @param date $data2 Maior data no formato americano Y-m-d
	 * @param char $invalo Tipo de invalo: m=mes, d=dia, h=hora, n=minuto
	 * @return integer
	 */
	static public function diff($data1, $data2, $invalo)
	{
		$q = 1;
		switch ($invalo) {
			case 'm': $q *= 30;
			case 'd': $q *= 24;
			case 'h': $q *= 60;
			case 'n': $q *= 60;
		}
		return intval((strtotime($data2) - strtotime($data1)) / $q);
	}
}