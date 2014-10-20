<?php

class App_Funcoes_Galeria
{
	function removeDir($dir) {
		if (is_dir($dir)) {
			$objects = scandir($dir);
			foreach ($objects as $object) {
				if ($object != "." && $object != "..") {
					if (filetype($dir."/".$object) == "dir") App_Funcoes_Galeria::removeDir($dir."/".$object); else unlink($dir."/".$object);
				}
			}
			reset($objects);
			rmdir($dir);
		} else {
			return false;
		}
		
		return true;
	} 
}