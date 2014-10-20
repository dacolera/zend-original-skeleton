<?php
#############################################
# Classe para admin de galerias e sub		#
# Desenvolvedor: Tiago Boross de Oliveira	#
# E-mail: tiago@vm2.com.br					#
# Data: 11/12/2006							#
#############################################

class galeriaThumb
{
	var $thumb_width = 100;		// largura default para imagem do thumbnail
	var $thumb_height = 100;	// altura default para imagem do thumbnail
	
	var $img_quality = 80;		// qualidade da imagem gerada
	var $img_opacity = 40; 		// porcentagem da opacidade quando temos 2 imagens
	var $use_grayscale = false; // escala de cinza do thumbnail
	var $resize_mode = 2; 		/** $resize_mode:
									0 = Descarta proporção
									1 = Mantém proporção entre largura e altura
									2 = smart cut
								*/
	/**
	* Inicializando a classe galeriaThumb
	* @param string	$format 	Formato do arquivo a ser gerado
	* @param int	$mode 		Processo no qual será redimensionada a imagem
	* @desc 					Inicializando a classe galeriaThumb
	*/
	function galeriaThumb($format = 'JPG', $mode = 1)
	{
		$this->format = strtolower($format);
		$this->resize_mode = $mode;
	}
	
	function set_grayscale()
	{
		$this->use_grayscale = true;
	}	
	
	/**
	* Criar o arquivo thumbnail
	* @param string $source     Caminho completo da pasta de origem das imagens
	* @param string $dest		Caminho completo da pasta de destino, onde serão salvos os arquivos gerados
	* @desc 					Cria o arquivo thumbnail do pasta $source, salvando em $dest
	*/
	function thumbnail($source, $dest)
	{
		$im = $this->_img_create_from($source);
		$source_w = ImageSX($im);
		$source_h = ImageSY($im);
		
	
		switch ($this->resize_mode)
		{
			// Descarta proporção
			case 0:
				$ni = $this->_img_create($this->thumb_width, $this->thumb_height);
				$this->_img_copy_resize($ni, $im, 0, 0, 0, 0, $this->thumb_width, $this->thumb_height, $source_w, $source_h);
				break;
			// Mantém proporção entre largura e altura
			case 1:
				if ($source_w > $source_h)
				{
					$thumb_width = $this->thumb_width;
					$thumb_height = ($source_h * $this->thumb_width) / $source_w;
				}
				else
				{
					$thumb_height = $this->thumb_height;
					$thumb_width = ($source_w * $this->thumb_width) / $source_h;
				}
				$ni = $this->_img_create($thumb_width, $thumb_height);
				$this->_img_copy_resize($ni, $im, 0, 0, 0, 0, $thumb_width, $thumb_height, $source_w, $source_h);
				break;
			// Smart Cut = Crop
			case 2:
			default:				
				$wm = $source_w / $this->thumb_width;
				$hm = $source_h / $this->thumb_height;
					
				$h_height = $this->thumb_height / 2;
				$w_height = $this->thumb_width / 2;
				
				$ni = $this->_img_create($this->thumb_width, $this->thumb_height);
				
				// landscape
				if ($source_w > $source_h)
				{
					$adjusted_width = $source_w / $hm;
					$half_width = $adjusted_width / 2;
					$int_width = $half_width - $w_height;
					$this->_img_copy_resize($ni, $im, 0, 0, 0, 0, $adjusted_width, $this->thumb_height, $source_w, $source_h);
				}
				// portrade
				elseif (($source_w < $source_h) || ($source_w == $source_h))
				{
					$adjusted_height = $source_h / $wm;
					$half_height = $adjusted_height / 2;
					$int_height = $half_height - $h_height;
					$this->_img_copy_resize($ni, $im, 0, 0, 0, 0, $this->thumb_width, $adjusted_height, $source_w, $source_h);
				}
				else
				{
					$this->_img_copy_resize($ni, $im, 0, 0, 0, 0, $this->thumb_width, $this->thumb_heigth, $source_w, $source_h);
				}
		}
		imagedestroy($im);
		if ($this->use_grayscale)
		{
			$ni = $this->_img_grayscale($ni);
		}
		$this->output($ni, $dest);
	}
	/**
	* Adiciona marca d'agua na imagem
	* @param string $image				Caminho completo dos arquivos de origem
	* @param string $watermark			Caminho completo onde está a imagem de marca d'água
	* @param string $new_file			Caminho completo onde você deseja salvar a imagem com a marca d'agua, se tiver vazio a imagem original será substituida
	* @param int $watermark_pos			Posição onde o arquivo onde $insertfile será inserido em $source
	*										0 = middle
	*										1 = top left
	*										2 = top right
	*										3 = bottom right
	*										4 = bottom left
	*										5 = top middle
	*										6 = middle right
	*										7 = bottom middle
	*										8 = middle left
	* @desc 							Adiciona a marca d'agua na imagem no lugar desejado.
	*/
	function watermark($image, $watermark, $new_file = '', $watermark_pos = 0)
	{
		if ($new_file == '')
		{
			$new_file = $image;
		}
		
		$im_image = $this->_img_create_from($image);
		$image_width = imageSX($im_image);
		$image_height = imageSY($im_image);

		$im_watermark = $this->_img_create_from($watermark);
		$water_width = imageSX($im_watermark);
		$water_height = imageSY($im_watermark);
		
		switch($watermark_pos)
		{
			case 0: //middle
				$pos_x = ( $image_width / 2 ) - ( $water_width / 2 );
				$pos_y = ( $image_height / 2 ) - ( $water_height / 2 );
				break;
			case 1:  //top left
				$pos_x = 0;
				$pos_y = 0;
				break;
			case 2: //top right
				$pos_x = $image_width - $water_width;
				$pos_y = 0;
				break;
			case 3: //bottom right
				$pos_x = $image_width - $water_width;
				$pos_y = $image_height - $water_height;
				break;
			case 4: //bottom left
				$pos_x = 0;
				$pos_y = $image_height - $water_height;
				break;
			case 5: //top middle
				$pos_x = ( ( $image_width - $water_width ) / 2 );
				$pos_y = 0;
				break;
			case 6: //middle right
				$pos_x = $image_width - $water_width;
				$pos_y = ( $image_height / 2 ) - ( $water_height / 2 );
				break;
			case 7: //bottom middle
				$pos_x = ( ( $image_width - $water_width ) / 2 );
				$pos_y = $image_height - $water_height;
				break;
			case 8: //middle left
				$pos_x = 0;
				$pos_y = ( $image_height / 2 ) - ( $water_height / 2 );
				break;
		}
		if (function_exists('imagecopymerge'))
		{
			imagecopymerge($im_image, $im_watermark, $pos_x, $pos_y, 0, 0, $water_width, $water_height, $this->img_opacity);
		}
		else
		{
			imagecopy($im_image, $im_watermark, $pos_x, $pos_y, 0, 0, $water_width, $water_height);
		}
		$this->output($im_image, $new_file);
	}
	
	/**
	* Converte a cor da imagem para escala de cinza
	* @param string $source     Caminho completo dos arquivos de origem
	* @param string $dest		Caminho completo onde serão salvos os thumbnails gerados
	* @desc 					Converte uma imagem colorida para escala de cinza
	*/
	function grayscale($source, $dest)
	{
		$im = $this->_img_create_from($source);
		$im = $this->_img_grayscale($im);
		$this->output($im, $dest);
	}
	
	/**
	* Saída da imagem
	* @param int $im			Identificador da imagem
	* @param string $filename	Caminho completo onde serão salvos os thumbnails gerados as file
	* @desc 					Saída da imagem para um arquivo ou no browser
	*/
	function output($im, $filename = '')
	{
		if ($filename == '')
		{
			switch($this->format)
			{
				case 'gif':		
					header("Content-type: image/gif");
					imageGif($im);
					break;
				case 'png':
					header("Content-type: image/png");
					imagePng($im);
				case 'bmp':
					header("Content-type: image/vnd.wap.wbmp");
				    imageWbmp($im);
					break;
				case 'jpg':
				case 'jpeg':
				default:
					header("Content-type: image/jpeg");
					imageJpeg($im, '', $this->img_quality);
			}
			@imagedestroy($im);
			exit;
		}
		else
		{
			switch($this->format)
			{
				case 'png':
					imagePng($im, $filename);
					break;
				case 'gif':
					// somente GD < ver. 1.6
					imageGif($im, $filename);
					break;
				case 'bmp':
					// somente GD ver 1.8 ou superior
					imageWbmp($im, $filename);
					break;
				case 'jpeg':
				case 'jpg':
				default:
					imageJpeg($im, $filename, $this->img_quality);
			}
			return true;
		}
	}

	
	// Funções Priv.
	function _img_grayscale($im)
	{
		$x = imagesx($im);
		$y = imagesy($im);
		for($i=0; $i<$y; $i++)
		{
			for($j=0; $j<$x; $j++)
			{
				$pos = imagecolorat($im, $j, $i);
				$f = imagecolorsforindex($im, $pos);
				$gst = $f['red']*0.15 + $f['green']*0.5 + $f['blue']*0.35;
				$col = imagecolorresolve($im, $gst, $gst, $gst);
				imagesetpixel($im, $j, $i, $col);
			}
		}
		return $im;
	}
	
	function _img_copy_resize(&$dst_im, $src_im, $dstX, $dstY, $srcX, $srcY, $dstW, $dstH, $srcW, $srcH)
	{
		if (function_exists('imagecopyresampled'))
		{
			if (!@ImageCopyResampled($dst_im, $src_im, $dstX, $dstY, $srcX, $srcY, $dstW, $dstH, $srcW, $srcH))
			{
				if (imagecopyresized($dst_im, $src_im, $dstX, $dstY, $srcX, $srcY, $dstW, $dstH, $srcW, $srcH))
				{
					return true;
				}
			}
			return true;
		}
		else
		{
			if (imagecopyresized($dst_im, $src_im, $dstX, $dstY, $srcX, $srcY, $dstW, $dstH, $srcW, $srcH))
			{
				return true;
			}
		}
		return false;
	}
	
	function _img_create($x_size, $y_size)
	{
		if (function_exists('imageCreateTrueColor'))
		{
			if ($im = imagecreatetruecolor($x_size, $y_size))
			{
				return $im;
			}
		}
		else
		{
			if ($im = imageCreate($x_size, $y_size))
			{
				return $im;
			}
		}
		$this->debug();
	}
	
	function _img_create_from($file)
	{
		$ext = $this->_get_ext($file);
		switch ($ext)
		{
			case 'jpeg':
			case 'jpg':
				$im = imageCreateFromJpeg($file);
				break;
			case 'png':
				$im = imageCreateFromPng($file);
				break;
			case 'gif':
				$im = imageCreateFromGif($file);
				break;
		}		
		return $im;
	}
	
	function _get_ext($file)
	{
		$ext_arr = split("[/\\.]", strtolower($file));
		$n = sizeof($ext_arr)-1;
		return $ext_arr[$n];
	}
	
	# Remove todos os arquivos de um diretório, sem apaga-lo
	function apagaThumbnail( $dir )
	{
		$arquivosNormais = glob($dir . "*");
		$arquivosOcultos = glob($dir . "\.?*");
		$arquivosGerais = array_merge($arquivosNormais, $arquivosOcultos);
		
		foreach ($arquivosGerais as $file)
		{
			# Pula pseudos links como por exemplo o diretório atual e o pai (./ e ../).
			if (preg_match("/(\.|\.\.)$/", $file))
			{
				continue;
			}
		
			if (is_file($file) === TRUE) {
				// Remove cada arquivo dentro deste diretório
				@unlink($file);
			
			}
		}
		return 1;
	}
	
}
?>