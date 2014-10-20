<?php

/**
 * Definição do objeto Módulo
 *
 * @category 	Model
 * @package 	Model_Entity
 * @subpackage 	Sistema
 * @author 		Eduardo Schmidt <eduschmidt10@hotmail.com>
 */
class App_Model_Entity_Sistema_Modulo extends App_Model_Entity_Abstract
{
	/**
	 * @var App_Model_Collection of App_Model_Entity_Sistema_Acao
	 */
	protected $objAcoes;

	public function __sleep()
	{
		$fields = array_merge(parent::__sleep(), array('objAcoes'));
		return $fields;
	}

	public function __wakeup()
	{
		parent::__wakeup();
		$this->setTable(App_Model_DAO_Sistema_Modulos::getInstance());
	}

	/**
	 * Recupera o código identificador do módulo
	 *
	 * @return integer
	 */
	public function getCodigo()
	{
		return (int) $this->mod_idModulo;
	}

	/**
	 * Define o nome do módulo
	 *
	 * @param string $value
	 * @return App_Model_Entity_Sistema_Modulo
	 */
	public function setNome($value)
	{
		$this->mod_nome = (string) $value;
		return $this;
	}

	/**
	 * Recupera o nome do módulo
	 *
	 * @return string
	 */
	public function getNome()
	{
		return (string) $this->mod_nome;
	}

	/**
	 * Define o rótulo de exibição do módulo
	 *
	 * @param string $value
	 * @return App_Model_Entity_Sistema_Modulo
	 */
	public function setRotulo($value)
	{
		$this->mod_rotulo = (string) $value;
		return $this;
	}

	/**
	 * Recupera o rótulo de exibição do módulo
	 *
	 * @return string
	 */
	public function getRotulo()
	{
		return (string) $this->mod_rotulo;
	}

	/**
	 * Recupera as ações relacionadas ao módulo
	 *
	 * @return App_Model_Collection of App_Model_Entity_Sistema_Acao
	 */
	public function getAcoes()
	{
		if (null === $this->objAcoes) {
			$this->objAcoes = $this->findDependentRowset(App_Model_DAO_Sistema_Acoes::getInstance(), 'Modulo');
			foreach ($this->objAcoes as $acao) {
				$acao->setModulo($this);
			}
			$this->objAcoes->rewind();
		}
		return $this->objAcoes;
	}
}