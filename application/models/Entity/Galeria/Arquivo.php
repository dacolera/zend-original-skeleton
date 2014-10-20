<?php

class App_Model_Entity_Galeria_Arquivo extends App_Model_Entity_Abstract
{
	/**
	 * @var App_Model_Entity_Galeria
	 */
	protected $objGaleria = null;

	public function __sleep()
	{
		$fields = array_merge(parent::__sleep(), array('objGaleria'));
		return $fields;
	}

	public function __wakeup()
	{
		parent::__wakeup();
		$this->setTable(App_Model_DAO_Galerias_Arquivos::getInstance());
	}

	public function save()
	{
		$updating = (bool) $this->getCodigo();
		parent::save();

		if ($updating == true) {
			$galeriaPai = $this->getGaleria();
			do {
				if($galeriaPai->getPai() != null) {
					$galeriaPai = $galeriaPai->getPai();
				}
	
			} while ($galeriaPai->getPai() != null);
			$galeriaPai->update($this);
		}
	}

	public function delete()
	{
		$galeriaPai = $this->getGaleria();
		do {
			if($galeriaPai->getPai() != null) {
				$galeriaPai = $galeriaPai->getPai();
			}
		} while ($galeriaPai->getPai() != null);
		$galeriaPai->delete($this);

		parent::delete();
	}

	/**
	 * Recupera o código identificador da imagem
	 * 
	 * @return integer
	 */
	public function getCodigo()
	{
		return $this->gal_arq_idArquivo;
	}

	/**
	 * Define a qual galeria o arquivo pertence
	 * 
	 * @param  $value
	 * @return App_Model_Entity_Galeria_Abstract
	 */
	public function setGaleria($value)
	{
		$this->gal_arq_idGaleria = $value->getCodigo();
		$this->objGaleria = $value;
		return $this;
	}

	/**
	 * Recupera a galeria a qual a imagem pertence
	 * 
	 * @return App_Model_Entity_Galeria_Abstract
	 */
	public function getGaleria()
	{
		if (null === $this->objGaleria && $this->gal_arq_idArquivo) {
			$daoGalerias = App_Model_DAO_Galerias::getInstance();
			if(is_numeric($this->gal_arq_idGaleria)) {
				$this->objGaleria = $daoGalerias->fetchRow($daoGalerias->select()->where('gal_idGaleria = ?', $this->gal_arq_idGaleria));
			} else {
				$this->objGaleria = $daoGalerias->getGaleria($this->gal_arq_idGaleria);
			}
			unset($daoGalerias);
		}
		return $this->objGaleria;
	}

	/**
	 * Define o nome da imagem
	 * 
	 * @param string $value
	 * @return App_Model_Entity_Galeria_Imagem
	 */
	public function setNome($value)
	{
		$this->gal_arq_nome = (string) $value;
		return $this;
	}

	/**
	 * Recupera o nome da imagem
	 * 
	 * @return string
	 */
	public function getNome()
	{
		return (string) $this->gal_arq_nome;
	}

	/**
	 * Define a data de criação do arquivo
	 * @param datetime $value
	 * @return App_Model_Entity_Galeria_Arquivo
	 */
	public function setData($value)
	{
		$this->gal_arq_data = $value;
		return $this;
	}

	/**
	 * Recupera a data de criação do arquivo
	 * 
	 * @return datetime
	 */
	public function getData()
	{
		return $this->gal_arq_data;
	}
	
	/**
	 * Recupera o caminho físico do arquivo
	 * 
	 * @return string
	 */
	public function getFilePath($base = null)
	{
		return $this->getGaleria()->getFilePath() . DIRECTORY_SEPARATOR . ($base != null ? $base.DIRECTORY_SEPARATOR : null) . $this->getNome();
	}

	/**
	 * Recupera o caminho navegável do arquivo
	 * 
	 * @return string
	 */
	public function getBasePath($base = null)
	{
		$path = $this->getGaleria()->getBasePath();
		return ($base != null ? "{$path}/{$base}" : $path) . "/{$this->getNome()}";
	}
}