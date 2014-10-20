<?php

/**
 * Defini��o do objeto A��o
 *
 * @category 	Model
 * @package 	Model_Entity
 * @subpackage 	Sistema
 * @author 		Eduardo Schmidt <eduschmidt10@hotmail.com>
 */
class App_Model_Entity_Sistema_Acao extends App_Model_Entity_Abstract
{
	/**
	 * @var App_Model_Entity_Sistema_Modulo
	 */
	protected $objModulo = null;

	public function __sleep()
	{
		$fields = array_merge(parent::__sleep(), array('objModulo'));
		return $fields;
	}

	public function __wakeup()
	{
		parent::__wakeup();
		$this->setTable(App_Model_DAO_Sistema_Acoes::getInstance());
	}

	/**
	 * Recupera o c�digo identificador da a��o
	 *
	 * @return integer
	 */
	public function getCodigo()
	{
		return (int) $this->mod_acao_idAcao;
	}

	/**
	 * Define o m�dulo da a��o
	 *
	 * @param App_Model_Entity_Sistema_Modulo $value
	 * @return App_Model_Entity_Sistema_Acao
	 */
	public function setModulo(App_Model_Entity_Sistema_Modulo $value)
	{
		$this->objModulo = $value;
		$this->mod_acao_idModulo = $value->getCodigo();
		return $this;
	}

	/**
	 * Recupera o m�dulo da a��o
	 *
	 * @return App_Model_Entity_Sistema_Modulo
	 */
	public function getModulo()
	{
		if (null === $this->objModulo) {
			$this->objModulo = $this->findParentRow(App_Model_DAO_Sistema_Modulos::getInstance(), 'Modulo');
		}
		return $this->objModulo;
	}

	/**
	 * Define o nome da a��o
	 *
	 * @param string $value
	 * @return App_Model_Entity_Sistema_Acao
	 */
	public function setNome($value)
	{
		$this->mod_acao_nome = (string) $value;
		return $this;
	}

	/**
	 * Recupera o nome da a��o
	 *
	 * @return string
	 */
	public function getNome()
	{
		return (string) $this->mod_acao_nome;
	}

	/**
	 * Define o r�tulo de exibi��o da a��o
	 *
	 * @param string $value
	 * @return App_Model_Entity_Sistema_Acao
	 */
	public function setRotulo($value)
	{
		$this->mod_acao_rotulo = (string) $value;
		return $this;
	}

	/**
	 * Recupera o r�tulo de exibi��o da a��o
	 *
	 * @return string
	 */
	public function getRotulo()
	{
		return (string) $this->mod_acao_rotulo;
	}
}