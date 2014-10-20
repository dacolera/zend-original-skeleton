<?php

class App_Funcoes_Busca
{
	/**
	 * Gera o link para a página atual, colocando a paginação
	 *
	 * @param int $pagina
	 * @param array $params
	 * @param string $paginatorVar
	 * @return string
	 */
	public static function linkPaginacao($pagina = null, array $params = null, array $ignore = null, $paginatorVar = 'p')
	{
		$queryString = '';

		//qual é a página que está sendo paginada
		$urlAtual = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '?'));
		if (!$urlAtual) $urlAtual = $_SERVER['REQUEST_URI'];
		if(substr($urlAtual, strlen($urlAtual)-1, 1) != '/') {
			$urlAtual .= '/';
		}
		$urlAtual = "http://{$_SERVER['HTTP_HOST']}". htmlentities($urlAtual);

	    //link da página atual com toda query string
	    $queryParams = array();
	 	foreach($_GET as $k => $v) {
	 		if (($k <> $paginatorVar) && (!is_array($params) || !in_array($k, array_keys($params)))) {
	 			if ($ignore == null || (is_array($ignore) && !in_array($k, $ignore))) {
					$queryParams[] = sprintf("%s=%s", urlencode(htmlentities($k)), urlencode($v));
	 			}
	 		}
	 	}

	 	if (null != $pagina) {
	 		$queryParams[] = "{$paginatorVar}={$pagina}";
	 	}

	 	if (null != $params && count($params)) {
	 		foreach ($params as $k => $v) {
	 			$queryParams[] = sprintf("%s=%s", htmlentities(urldecode($k)), htmlentities(urldecode($v)));
	 		}
	 	}

	 	$queryString = implode('&', $queryParams);
	 	if (strlen($queryString)) $queryString = "?{$queryString}";

		return "{$urlAtual}{$queryString}";
	}

	/**
	 * Retorna o range de valores para filtrar a busca
	 *
	 * @param float $valorMinimo
	 * @param float $valorMaximo
	 * @param int $intervaloMaximo
	 * @param int $multiplo
	 * @return array
	 */
	public static function rangePreco($valorMinimo, $valorMaximo, $intervaloMaximo=5, $multiplo=5)
	{
		$retorno = array();

		$mod = ceil((($valorMaximo+$valorMinimo)/2)-$valorMinimo);
		if($mod > $intervaloMaximo) { $mod = $intervaloMaximo; }
		if($mod == 0) { return $retorno; }

		$chave = ceil(($valorMaximo-$valorMinimo)/$mod);

		// Arredondamento para cima
		$chave = ceil($chave/$multiplo)*$multiplo;

		$min = $valorMinimo;
		$max = $valorMinimo + $chave;

		for($i=0; $i<$mod; $i++) {
			if($i == 0) {
				$descricao = "Até ". App_Funcoes_Money::toCurrency($max);
			} else if($i != $mod-1) {
				$descricao = App_Funcoes_Money::toCurrency($min) ." à ". App_Funcoes_Money::toCurrency($max);
			} else {
				$max = $max + 0.01;
				$descricao = "Acima de ". App_Funcoes_Money::toCurrency($min);
			}

			$retorno[] = array('vMin' => $min, 'vMax' => $max, 'descricao' => $descricao);

			$min = ($valorMinimo + $chave) + 0.01;
			$max = ($min + $chave < $valorMaximo ? $min + $chave : $valorMaximo) - 0.01;
			$valorMinimo = $valorMinimo + $chave;
		}

		// Se tem apenas um item, esvazia o array
		if(count($retorno) == 1) array_shift($retorno);

		return $retorno;
	}
}