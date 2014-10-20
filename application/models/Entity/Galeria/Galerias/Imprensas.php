<?php
class App_Model_Entity_Galeria_Galerias_Imprensas extends App_Model_Entity_Galeria_Abstract 
{
	protected $codigo = 'imprensas';
	protected $nome = 'Imprensa Imagens';

	private $thumbs  = array(
		'thumb' => array('w'=>59, 'h'=>59)
	);

	public function init()
	{
		$this->filePath = Zend_Registry::get('config')->paths->site->file .'/images/imprensa';
		$this->basePath = Zend_Registry::get('config')->paths->site->base .'/images/imprensa';
	}

	public function receive($file, App_Model_Entity_Galeria_Arquivo $arquivo)
	{
		$adapter = new Zend_File_Transfer_Adapter_Http();
		$adapter->addValidator('Extension', false, array('jpg', 'gif', 'png'));
		$upload = $adapter->getFileInfo($file);
		
		$fileName = App_Funcoes_SEO::toString(substr($upload[$file]['name'], 0 , strrpos($upload[$file]['name'], '.')));
		if (strlen($fileName) > 60) {
			throw new Exception('Tamanho máximo de 60 caractéres para o nome do arquivo excedido.');
		}
		
		//vendo se ja tem arquivo com o mesmo nome
		$ext = strtolower(substr($upload[$file]['name'], strrpos($upload[$file]['name'], '.')));
		$fileName = App_Funcoes_SEO::renameFile($fileName, $ext, $arquivo->getGaleria()->getFilePath());
		
		$arquivo->setNome($fileName);
		$filePath = "{$arquivo->getGaleria()->getFilePath()}/{$fileName}";

		$adapter->addFilter('Rename', $filePath, $file);
		if ($adapter->receive() == true) {
			chmod($filePath, 0777);

			require 'resizeGD.php';
			foreach ($this->thumbs as $path => $size) {
				$thumbPath = dirname($arquivo->getFilePath()) ."/{$path}/". basename($arquivo->getFilePath());
				if (!file_exists(dirname($thumbPath))) {
					mkdir(dirname($thumbPath), 0777);
					chmod(dirname($thumbPath), 0777);
				}
				$resizeGD = new resizeGD($arquivo->getFilePath());
				$resizeGD->resizeToMaxWidthHeight($size['w'], $size['h']);
				$resizeGD->save($ext, $thumbPath);

				unset($resizeGD);
			}
		} else {
			foreach ($adapter->getMessages() as $validator => $message) {
				throw new Exception($message);
			}
		}
	}
	
	public function updateFile(App_Model_Entity_Galeria_Arquivo $oldFile, App_Model_Entity_Galeria_Arquivo $file) {
		
		foreach ($this->thumbs as $path => $size) {
			$oldPath = dirname($oldFile->getFilePath()) ."/{$path}/". basename($oldFile->getFilePath());
			$newPath = dirname($file->getFilePath()) ."/{$path}/". basename($file->getFilePath());
			
			if(!rename($oldPath, $newPath)) {
				throw new Exception("Falha ao renomear o arquivo: {$file->getNome()}");
			}
		}
		
		// renomeando a pasta principal
		if(!rename($oldFile->getFilePath(), $file->getFilePath())) {
			throw new Exception("Falha ao renomear o arquivo: {$file->getNome()}");
		}
	}

	public function delete(App_Model_Entity_Galeria_Arquivo $arquivo)
	{
		foreach ($this->thumbs as $path => $size) {
			$thumbPath = dirname($arquivo->getFilePath()) ."/{$path}/". basename($arquivo->getFilePath());
			if (file_exists($thumbPath)) {
				unlink($thumbPath);
			}
		}

		if (file_exists($arquivo->getFilePath())) {
			unlink($arquivo->getFilePath());
		}
	}
}