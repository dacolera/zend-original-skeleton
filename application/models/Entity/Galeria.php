<?php

class App_Model_Entity_Galeria extends App_Model_Entity_Abstract
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
		$this->setTable(App_Model_DAO_Galerias::getInstance());
	}

	public function save()
	{
		$inserting = !$this->getCodigo();
		$oldNome = ($inserting ? null : $this->_cleanData['gal_nome']);

		try {
			$this->getTable()->getAdapter()->beginTransaction();
			$transaction = true;
		} catch (Exception $e) {
			$transaction = false;
		}

		try {
			parent::save();

			if ($inserting) {
				//cria fisicamente o diretório da galeria
				if (mkdir($this->getFilePath(), 0777)) {
					chmod($this->getFilePath(), 0777);
				} else {
					throw new Exception("Falha ao criar diretório: {$this->getFilePath()}");
				}
			} else {
				//altera o nome do diretório
				$oldPath = preg_replace("/{$this->getNome()}/", "/{$oldNome}/", $this->getFilePath());
				if(!rename($oldPath, $this->getFilePath())) {
					throw new Exception("Falha ao renomear a galeria: {$this->getNome()}");
				}
			}

			if ($transaction == true) {
				$this->getTable()->getAdapter()->commit();
			}
		} catch (Exception $e) {
			if ($transaction == false) {
				$this->getTable()->getAdapter()->rollBack();
			}
			throw $e;
		}
	}

	public function delete()
	{
		try {
			$this->getTable()->getAdapter()->beginTransaction();
			$transaction = true;
		} catch (Exception $e) {
			$transaction = false;
		}

		try {
			//remove os arquivos da galeria
			foreach ($this->getArquivos() as $arquivo) {
				$arquivo->delete();
			}
			//remove as galerias filhas
			foreach ($this->getFilhos() as $filho) {
				$filho->delete();
			}

			//remove fisicamente o diretório da galeria
			if (is_dir($this->getFilePath()) && !rmdir($this->getFilePath())) {
				throw new Exception("Não foi possível remover a galeria: {$this->getFilePath()}");
			}

			parent::delete();

			if ($transaction == true) {
				$this->getTable()->getAdapter()->commit();
			}
		} catch (Exception $e) {
			if ($transaction == true) {
				$this->getTable()->getAdapter()->rollBack();
			}
			throw $e;
		}
	}

	function getCodigo()
	{
		return $this->gal_idGaleria;
	}

	function getNome()
	{
		return $this->gal_nome;
	}
	
	function setNome($value)
	{
		$this->gal_nome = (string) $value;
		return $this;
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
		if (null === $this->objGaleriaPai) {
			$daoGalerias = App_Model_DAO_Galerias::getInstance();
			if(is_numeric($this->gal_idGaleriaPai)) {
				$this->objGaleriaPai = $daoGalerias->fetchRow($daoGalerias->select()->where('gal_idGaleria = ?', $this->gal_idGaleriaPai));
			} else {
				$this->objGaleriaPai = $daoGalerias->getGaleria($this->gal_idGaleriaPai);
			}
			unset($daoGalerias);
		}
		return $this->objGaleriaPai;
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
		$path = preg_replace("/(\/$)/", '', ($this->getPai() !== null ? $this->getPai()->getFilePath() : $this->filePath));
		return "{$path}/{$this->getNome()}";
	}

	/**
	 * Recupera o caminho navegável da galeria
	 *
	 * @return string
	 */
	public function getBasePath()
	{
		$path = preg_replace("/(\/$)/", '', ($this->getPai() !== null ? $this->getPai()->getBasePath() : $this->basePath));
		return "{$path}/{$this->getNome()}";
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