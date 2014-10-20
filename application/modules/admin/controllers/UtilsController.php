<?php

class UtilsController extends Zend_Controller_Action
{
	public function init()
	{
		$this->getFrontController()->setParam('noViewRenderer', true);
	}

	public function buscaEnderecoAction()
	{
		if (($cep = $this->getRequest()->getParam('cep', false)) != false)
		{
			/*$array = App_Funcoes_Correios::buscaEndereco($cep);
			echo Zend_Json::encode($array);*/
			
			$fp = fsockopen("www.cadastroweb.com.br", 80, $errno, $errstr, 30);
			if (!$fp) {
			    echo "$errstr ($errno)<br />\n";
			} else {
			    $out = "GET /consultaCep.php?cep={$cep} HTTP/1.1\r\n";
			    $out .= "Host: www.cadastroweb.com.br\r\n";
			    $out .= "Connection: Close\r\n\r\n";
			    fwrite($fp, $out);
			    
			    while(trim(fgets($fp,4096)) != '');
			    
			    $retorno = '';
			    while (!feof($fp)) {
			        $retorno .= fgets($fp, 128);
			    }
			    fclose($fp);
			}
			
			echo $retorno;
		}
	}
	
	public function resizeGaleriaAction()
	{
		$daoArquivos = App_Model_DAO_Galerias_Arquivos::getInstance();
		$imagem = $daoArquivos->fetchRow(
			$daoArquivos->select()->where('gal_arq_idArquivo = ?', $this->getRequest()->getParam('imagem'))
		);
		if ($imagem != null) {
			$ext = substr($imagem->getNome(), strrpos($imagem->getNome(), '.')+1);
			$width = $this->getRequest()->getParam('width');
			$height = $this->getRequest()->getParam('height');

			require_once 'resizeGD.php';
			if($ext == 'swf' || $ext == 'flv') {
				$resizeGD = new resizeGD(Zend_Registry::get('config')->paths->galeria->file . '/video.png');
			} else {
				$thumb = Zend_Registry::get('config')->paths->admin->file."/images/icons/extensions/{$ext}.gif";
				if (file_exists($thumb)) {
					$resizeGD = new resizeGD($thumb);
				} else {
					$resizeGD = new resizeGD($imagem->getBasePath());
				}
			}

			$resizeGD->resizeToMaxWidthHeight($width, $height);
			$resizeGD->show();
		}
	}
}