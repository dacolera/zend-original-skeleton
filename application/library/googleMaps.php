<?php 

define("URL","http://maps.googleapis.com/maps/api/geocode/json?address=");
class GoogleMaps
{
	public function getDados($endereco){
		$url = URL.urlencode(utf8_encode($endereco))."&sensor=true";
		$handle = fopen($url,"r");
		
		$contents = '';

		while (!feof($handle)) {
			$contents .= fread($handle, 8192);
		}
		
		fclose($handle);
		return json_decode($contents);
	}
}