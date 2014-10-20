<?php
#####################################################
#	Classe para redimensionamento de imagens por GD	#
#	Desenvolvedor: Pierre Delmotte					#
#	Data: 02/06/2006								#
#####################################################

class resizeGD
{
	var $im;
	var $imResized;
	var $x;
	var $y;
	var $ext;

	function resizeGD($file)
	{
		$this->doLoad($file);
	}
    
	function doLoad($file)
	{
		$tmp = explode('.', $file);
		$this->ext = $tmp[count($tmp)-1];
		if ( $this->ext == 'jpg' && function_exists("imagecreatefromjpeg") )	$this->im = imagecreatefromjpeg($file);
		if ( $this->ext == 'gif' && function_exists("imagecreatefromgif") ) $this->im = imagecreatefromgif($file);
		if ( $this->ext == 'png' && function_exists("imagecreatefrompng") )		$this->im = imagecreatefrompng($file);

		$this->x = imagesx($this->im);	#width original
		$this->y = imagesy($this->im);	#height original
	}

	function resizeToWidth($width)
	{
		$perc = (100*$width)/$this->x;

		$height = floor(($this->y/100)*$perc);

		$this->imResized = imagecreatetruecolor($width,$height);
		#Copia a imagem redimensionada pro imResized
		imagecopyresampled($this->imResized, $this->im, 0, 0, 0, 0, $width, $height, $this->x, $this->y);
	}

	function resizeToMaxWidthHeight($w, $h)
	{
		if ( $this->x > $w ) {
			$perc = (100*$w)/$this->x;

			$width	= $w;
			$height	= floor(($this->y/100)*$perc);
		} else {
			$width	= $this->x;
			$height = $this->y;
		}

		if ( $height > $h ) {
			$perc = (100*$h)/$height;

			$height	= $h;
			$width	= floor(($width/100)*$perc);
		}

		$this->imResized = imagecreatetruecolor($width,$height);
		
		if(($this->ext == 'png') OR ($this->ext == 'gif')){
			imagealphablending($this->imResized, false);
			imagesavealpha($this->imResized,true);
			$transparent = imagecolorallocatealpha($this->imResized, 255, 255, 255, 127);
			imagefilledrectangle($this->imResized, 0, 0, $width, $height, $transparent);
        }
		
		#Copia a imagem redimensionada pro imResized
		imagecopyresampled($this->imResized, $this->im, 0, 0, 0, 0, $width, $height, $this->x, $this->y);
	}

	function show()
	{
		header("Content-type: image/png");
		imagepng($this->imResized);
		/*switch ( $this->ext )
		{
			case 'jpg':
				header("Content-type: image/jpeg");
				imagejpeg($this->imResized, null, 100);
			break;
			case 'gif':
				header("Content-type: image/gif");
				imagegif($this->imResized);
			break;
			case 'png':
				header("Content-type: image/png");
				imagepng($this->imResized);
			break;
		}*/
	}

	function save($ext, $fullpath)
	{
		switch ( $ext )
		{
			case 'gif':
				header("Content-type: image/gif");
				imagegif($this->imResized, $fullpath);
			break;
			case 'png':
				header("Content-type: image/jpeg");
				imagepng($this->imResized, $fullpath);
			break;
			default:
				header("Content-type: image/jpeg");
				imagejpeg($this->imResized, $fullpath, 100);
			break;
		}
	}
}
?>