<?php

class App_Model_Entity_Galeria_Galerias_Downloads extends App_Model_Entity_Galeria_Abstract 
{
	protected $codigo = 'downloads';
	protected $nome = 'Imprensa Downloads';

	public function init()
	{
		$this->filePath = Zend_Registry::get('config')->paths->site->file .'/images/imprensa-downloads';
		$this->basePath = Zend_Registry::get('config')->paths->site->base .'/images/imprensa-downloads';
	}

	public function receive($file, App_Model_Entity_Galeria_Arquivo $arquivo)
	{
		$adapter = new Zend_File_Transfer_Adapter_Http();
		$adapter->addValidator('Extension', false, array('pdf', 'doc', 'docx'));
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
		} else {
			foreach ($adapter->getMessages() as $validator => $message) {
				throw new Exception($message);
			}
		}
	}
	
	public function updateFile(App_Model_Entity_Galeria_Arquivo $oldFile, App_Model_Entity_Galeria_Arquivo $file) {
		// renomeando a pasta principal
		if(!rename($oldFile->getFilePath(), $file->getFilePath())) {
			throw new Exception("Falha ao renomear o arquivo: {$file->getNome()}");
		}
	}

	public function delete(App_Model_Entity_Galeria_Arquivo $arquivo)
	{
		if (file_exists($arquivo->getFilePath())) {
			unlink($arquivo->getFilePath());
		}
	}
}