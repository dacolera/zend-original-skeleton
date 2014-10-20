<?php

class App_Funcoes_Rotulos
{
	public static $status = array('1' => 'Ativo', '0' => 'Inativo');

	public static $novidade = array('1' => 'Sim', '0' => 'N�o');
	
	public static $statusInscrito = array('1' => 'Ativo', '0' => 'Inativo', '2' => 'Pendende');
	
	public static $formaEnvio = array('SMTP' => 'SMTP', 'MAIL' => 'PHP MAIL');
	
	public static $modulosDinamicos = array('index', 'sports-app', 'como-chegar', 'nossas-promocoes');
	
	public static $tipoProgramacao = array(
		'1' => 'Futebol',
		'2' => 'Futebol Americano',
		'3' => 'Basquete',
		'4' => 'MMA',
		'5' => 'Voleibol',
		'6' => 'Rugby',
		'7' => 'T�nis',
		'8' => 'Jud�',
		'9' => 'Nata��o',
		'10' => 'Remo',
		'11' => 'Gin�stica Ol�mpica',
		'12' => 'Beisebol',
		'13' => 'Golf',
		'14' => 'Automobilismo',
		'15' => 'Ciclismo',
		'16' => 'Handebol',
		'17' => 'Motociclismo',
		'18' => 'Skate',
		'19' => 'Boxe',
		'20' => 'Atletismo'
	);
	
	/*
	 * index
	 * sports-app
	 * promocoes
	 */
	
	public static $tipoPessoa = array(
		'J' => 'Jur�dica', 
		'F' => 'F�sica'
	);
	
	public static $tipoBanner = array(
		'G' => 'Geral', 
		'U' => 'Por Unidade'
	);
	
	public static $ordem = array(
		'1' => '1',
		'2' => '2',
		'3' => '3',
		'4' => '4',
		'5' => '5'
	);
	
	public static $tipoPublicacao = array(
		'N' => 'Not�cias',
		'M' => 'M�dia', 
		'E' => 'Eventos',
		'P' => 'Publica��es',  
		//'B' => 'Refer�ncia Bibliogr�fica', 
		'C' => 'Cases de Sucesso' 
	);
	
	public static $tipoCurso = array(
		'ES' => 'Estrat�gia', 
		'IN' => 'Inova��o', 
		'EX' => 'Execu��o', 
		'MU' => 'Mudan�a'
	);	
	
	public static $UF = array(
		'AC' => 'Acre',
		'AL' => 'Alagoas',
		'AM' => 'Amazonas',
		'AP' => 'Amap�',
		'BA' => 'Bahia',
		'CE' => 'Cear�',
		'DF' => 'Distrito Federal',
		'ES' => 'Esp�rito Santo',
		'GO' => 'Goi�s',
		'MA' => 'Maranh�o',
		'MG' => 'Minas Gerais',
		'MS' => 'Mato Grosso do Sul',
		'MT' => 'Mato Grosso',
		'PA' => 'Par�',
		'PB' => 'Para�ba',
		'PE' => 'Pernambuco',
		'PI' => 'Piau�',
		'PR' => 'Paran�',
		'RJ' => 'Rio de Janeiro',
		'RN' => 'Rio Grande do Norte',
		'RO' => 'Rond�nia',
		'RR' => 'Roraima',
		'RS' => 'Rio Grande do Sul',
		'SC' => 'Santa Catarina',
		'SE' => 'Sergipe',
		'SP' => 'S�o Paulo',
		'TO' => 'Tocantins'
	);
	
	public static $meses =  array(
		'01' => 'Janeiro',
		'02' => 'Fevereiro',
		'03' => 'Mar�o',
		'04' => 'Abril',
		'05' => 'Maio',
		'06' => 'Junho',
		'07' => 'Julho',
		'08' => 'Agosto',
		'09' => 'Setembro',
		'10' => 'Outubro',
		'11' => 'Novembro',
		'12' => 'Dezembro'
	);
	
	public static $diaSemana =  array(
		'0' => 'Domingo',
		'1' => 'Segunda-Feira',
		'2' => 'Ter�a-Feira',
		'3' => 'Quarta-Feira',
		'4' => 'Quinta-Feira',
		'5' => 'Sexta-Feira',
		'6' => 'S�bado'
	);
	
	public static $assunto =  array(
		'1' => 'Assunto 1',
		'2' => 'Assunto 2',
		'3' => 'Assunto 3',
		'4' => 'Assunto 4',
		'5' => 'Assunto 5'
	);
}