<?php

abstract class App_Model_Entity_Galeria_Abstract extends App_Model_Entity_Abstract
{
	protected $objGaleriaPai = null;

	protected $filePath;
	protected $basePath;

	protected $objGaleriaFilhos = null;

	/**
	 * @var App_Model_Collection of App_Model_Entity_Galeria_Arquivo
	 */
	protected $objArquivos = null;

	public function __sleep()
	{
		$fields = array_merge(parent::__sleep(), array('objGaleriaPai', 'objGaleriaFilhos', 'objArquivos'));
		return $fields;
	}

	public function __wakeup()
	{
		parent::__wakeup();
		if(is_numeric($this->getCodigo())) {
			$this->setTable(App_Model_DAO_Galerias::getInstance());
		} 
		
		$this->init();
	}

	public function updateFile(App_Model_Entity_Galeria_Arquivo $oldFile, App_Model_Entity_Galeria_Arquivo $file) 
	{
		// renomeando a pasta principal
		if(!rename($oldFile->getFilePath(), $file->getFilePath())) {
			throw new Exception("Falha ao renomear o arquivo: {$file->getNome()}");
		}

		// renomenado as pastas auxiliares
		if (isset($this->imagensAuxiliares)) {
			foreach ($this->imagensAuxiliares as $key => $dirAux) {
				if(!rename($oldFile->getFilePath($key), $file->getFilePath($key))) {
					throw new Exception("Falha ao renomear o arquivo: {$file->getNome()}");
				}
			}
		}
	}

	abstract public function receive($file, App_Model_Entity_Galeria_Arquivo $imagem);

	public function getCodigo()
	{
		return $this->codigo;
	}

	public function getNome()
	{
		return $this->nome;
	}

	/**
	 * Define a galeria pai da galeria
	 * 
	 * @param $value
	 * @return App_Model_Entity_Galeria
	 */
	public function setPai($value)
	{
		$this->objGaleriaPai = $value;
		$this->gal_idGaleriaPai = $value->getCodigo();
		return $this;
	}

	/**
	 * Recupera a galeria pai da galeria
	 * 
	 * @return App_Model_Entity_Galeria or NULL
	 */
	public function getPai()
	{
		return null;
	}

	/**
	 * Recupera as galerias filhos da galeria
	 * 
	 * @return App_Model_Collection of App_Model_Entity_Galeria
	 */
	public function getFilhos()
	{
		if (null === $this->objGaleriaFilhos) {
			$daoGalerias = App_Model_DAO_Galerias::getInstance();
			$this->objGaleriaFilhos = $daoGalerias->fetchAll(
				$daoGalerias->select()->where('gal_idGaleriaPai = ?', $this->getCodigo())
			);
			unset($daoGalerias);

			foreach ($this->objGaleriaFilhos as $galeria) {
				$galeria->setPai($this);
			}
			$this->objGaleriaFilhos->rewind();
		}
		return $this->objGaleriaFilhos;
	}

	/**
	 * Recupera o caminho físico da galeria
	 *
	 * @return string
	 */
	public function getFilePath()
	{
		if (null != $this->getPai()) {
			return $this->getPai()->getFilePath() . DIRECTORY_SEPARATOR . $this->getNome();
		} else {
			return $this->filePath;
		}
	}

	/**
	 * Recupera o caminho navegável da galeria
	 *
	 * @return string
	 */
	public function getBasePath()
	{
		if (null != $this->getPai()) {
			return "{$this->getPai()->getBasePath($base)}/{$this->getNome()}";
		} else {
			return $this->basePath;
		}
	}

	/**
	 * Recupera a lista de arquivos da galeria
	 * 
	 * @return App_Model_Collection of App_Model_Entity_Galeria_Arquivo
	 */
	public function getArquivos()
	{
		if (null === $this->objArquivos ) {
			$daoArquivos = App_Model_DAO_Galerias_Arquivos::getInstance();
			$this->objArquivos = $daoArquivos->fetchAll(
				$daoArquivos->select()->where('gal_arq_idGaleria = ?', $this->getCodigo())
			);
			
			foreach ($this->objArquivos as $arquivo) {
				$arquivo->setGaleria($this);
			}
			$this->objArquivos->rewind();
		} 
		
		return $this->objArquivos;
	}
}