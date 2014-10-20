<?php

class App_Funcoes_Correios
{
	const FRETE_SEDEX = '40010';

	public static function buscaEndereco($cep)
	{
		if(APPLICATION_ENV == 'production') {
		
			$url = "http://comercio.locaweb.com.br/correios/calcula_sedex.asp?cepDest={$cep}&urlback=void";
			$dom = new DOMDocument();
			$dom->preserveWhiteSpace = FALSE;
			$dom->loadHTML(file_get_contents($url));
	
			$array = array();
			$nodes = $dom->getElementsByTagName('input');
			foreach ($nodes as $node) {
				$array[$node->getAttribute('name')] = $node->getAttribute('value');
			}
			App_Funcoes_UTF8::decode($array);
			return $array;
			
		} else {
			$dadosRetorno = array(
				'endereco' => 'Al. Rio Negro',
				'bairro' => 'Alphaville',
				'estado' => 'SP',
				'cidade' => 'Barueri',
				'erro_descricao' => ''
			);
		}
		return $dadosRetorno;
	}

	public static function calculaFrete($empresa, $senha, $origem, $destino, $peso, $formato, $comprimento, $altura, $largura, $maoPropria, $valorDeclarado, $avisoRecebimento, $servico, $diametro)
	{
		$url = "http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa={$empresa}&sDsSenha={$senha}&sCepOrigem={$origem}&sCepDestino={$destino}&nVlPeso={$peso}&nCdFormato={$formato}&nVlComprimento={$comprimento}&nVlAltura={$altura}&nVlLargura={$largura}&sCdMaoPropria={$maoPropria}&nVlValorDeclarado={$valorDeclarado}&sCdAvisoRecebimento={$avisoRecebimento}&nCdServico={$servico}&nVlDiametro={$diametro}&StrRetorno=xml";		
		$xml = simplexml_load_file($url);
		$arrayRetorno = array();
		$count = 0;
		
		$array = array(
			'Endereco_Frete' => '',
			'erro_descricao' => ''
		);
		foreach( $xml->cServico as $servicoCorreio ) {
			if($servicoCorreio->Erro == 0) {
				$array = array(
					'Endereco_Frete' => $servicoCorreio->Valor,
					'erro_descricao' => ''
				);
			} else {
				$array = array(
					'Endereco_Frete' => $servicoCorreio->Erro,
					'erro_descricao' => $servicoCorreio->MsgErro
				);
			}
		}
		return $array;
	}
}