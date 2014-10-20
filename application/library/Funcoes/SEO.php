<?php

class App_Funcoes_SEO
{
	/**
	 * Remove os caracteres especiais de uma string
	 *
	 * @param string 
	 * @return string
	 */
	public static function toString($string)
	{
		$param = strtolower($string);
		
		$str = htmlentities($param);
		$str = preg_replace('/&((?i)[a-z]{1,2})(?:grave|accent|acute|circ|tilde|uml|ring|lig|cedil|slash);/', '$1', $str);
		$str = str_replace(array('&ETH;', '&eth;', '&THORN;', '&thorn;'), array('dh', 'd', 'TH', 'th'), $str);
	
		$param = str_replace(array('_', ' '), array('-', '-'), $str);
		$param = preg_replace('![^\w_-]!', '', $param);
		$param = preg_replace('!-{2,}!', '-', $param);
		$param = preg_replace('!^-+|-+$!', '', $param);

		return $param;
		
	}

	/**
	 * Verifica se o arquivo já existe e renomeia caso seja necessário
	 *
	 * @param string $fileName
	 * @param string $ext
	 * @param string $path
	 * @return string
	 */
	public static function renameFile($fileName, $ext, $path) {
		$numero = 1;
		$filePath = "{$path}/{$fileName}{$ext}";
		do {
			if(file_exists($filePath)) {
				$filePath = "{$path}/{$fileName}_{$numero}{$ext}";
				$numero++;
			}

		} while (file_exists($filePath));

		return basename($filePath);
	}
	
	/**
	 * Verifica se o diretório já existe e renomeia caso seja necessário
	 *
	 * @param string $dirName
	 * @param string $path
	 * @return string
	 */
	public static function renameDir($dirName, $path) {
		$numero = 0;
		$filePath = "{$path}/{$dirName}";
		
		if(is_dir($filePath)) {
			do {
				if(is_dir($filePath)) {
					$filePath = "{$path}/{$dirName}_{$numero}";
					$numero++;
				}
	
			} while (is_dir($filePath));
		} else {
			return $dirName;
		}

		return "{$dirName}_{$numero}";
	}
	
}