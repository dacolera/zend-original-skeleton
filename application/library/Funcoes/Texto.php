<?php
class App_Funcoes_Texto
{
	public static function cortar($str, $letras=30)
	{
		$str = trim($str);							#Tira os espa�os		
		if ( strlen($str) > $letras ) {				#S� corta se tem mais que o valor a cortar
				
			$ret = substr($str,0,$letras);			#corta a string
	
			if ( substr($str,$letras,1) != " " ) {	#Se depois do que cortou vinha espa�o ele deixa assim, senao ele tira o pedaco do final
				$tmp = explode(' ',$ret);
				unset($ret);
				for ( $i=0; $i<count($tmp)-1; $i++ ) {
					if ( !$ret )	$ret = $tmp[$i];
					else 			$ret .= ' '. $tmp[$i];
				}
			}
			
			$ret = trim($ret);
			return $ret .'...';	#Retorna com o ...
		} else {
			return $str;	#Se a string � <ou= do que o que precisa mostrar, mostra ela intocada
		}
	}
}