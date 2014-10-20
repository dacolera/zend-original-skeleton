<?php

/**
 * Definição do objeto Perfil
 *
 * @category 	Model
 * @package 	Model_Entity
 * @subpackage 	Sistema
 * @author 		Eduardo Schmidt <eduschmidt10@hotmail.com>
 */
class App_Model_Entity_Sistema_Perfil extends App_Model_Entity_Abstract
{
	/**
	 * @var App_Model_Collection of App_Model_Entity_Sistema_Usuario
	 */
	protected $objUsuarios = null;

	/**
	 * @var App_Model_Collection of App_Model_Entity_Sistema_Acao
	 */
	protected $objPermissoes = null;

	public function __sleep()
	{
		$fields = array_merge(parent::__sleep(), array('objUsuarios'));
		return $fields;
	}

	public function __wakeup()
	{
		parent::__wakeup();
		$this->setTable(App_Model_DAO_Sistema_Perfis::getInstance());
	}

	public function save()
	{
		$filters = array(
			'*' => new Zend_Filter_StringTrim()
		);

		$validators = array(
			'per_nome' => array(
				Zend_Filter_Input::ALLOW_EMPTY => false,
				new Zend_Validate_StringLength(4, 40)
			)
		);

		//verifica a consistência dos dados
		$this->validate($filters, $validators, $this->toArray());

		//persiste os dados no banco
		$this->getTable()->getAdapter()->beginTransaction();
		try {
			parent::save();

			$daoPermissoes = App_Model_DAO_Sistema_Permissoes::getInstance();
			$this->getPermissoes(); //força o carregamento das permissões
			$daoPermissoes->delete($daoPermissoes->getAdapter()->quoteInto('perm_idPerfil = ?', $this->getCodigo()));
			foreach ($this->getPermissoes() as $acao) {
				$daoPermissoes->insert(array(
					'perm_idPerfil' => $this->getCodigo(),
					'perm_idAcao' => $acao->getCodigo()
				));
			}

			$this->getTable()->getAdapter()->commit();
		} catch (Exception $e) {
			$this->getTable()->getAdapter()->rollBack();
			throw $e;
		}
	}

	/**
	 * Recupera o código identificador do perfil
	 * 
	 * @return integer
	 */
	public function getCodigo()
	{
		return (int) $this->per_idPerfil;
	}

	/**
	 * Define o nome do perfil
	 * 
	 * @param string $value
	 * @return App_Model_Entity_Sistema_Perfil
	 */
	public function setNome($value)
	{
		$this->per_nome = (string) $value;
		return $this;
	}

	/**
	 * Recupera o nome do perfil
	 * @return string
	 */
	public function getNome()
	{
		return (string) $this->per_nome;
	}

	/**
	 * Recupera os usuários pertencentes ao perfil
	 * 
	 * @return App_Model_Collection of App_Model_Entity_Sistema_Usuario
	 */
	public function getUsuarios()
	{
		if (null === $this->objUsuarios) {
			if ($this->getCodigo()) {
				$this->objUsuarios = $this->findDependentRowset(App_Model_DAO_Sistema_Usuarios::getInstance(), 'Perfil');
				foreach ($this->objUsuarios as $usuario) {
					$usuario->setPerfil($this);
				}
				$this->objUsuarios->rewind();
			} else {
				$this->objUsuarios = App_Model_DAO_Sistema_Usuarios::getInstance()->createRowset();
			}
		}
		return $this->objUsuarios;
	}

	/**
	 * Recupera as permissões do perfil
	 * 
	 * @return App_Model_Collection of App_Model_Entity_Sistema_Acao
	 */
	public function getPermissoes()
	{
		if (null === $this->objPermissoes) {
			if ($this->getCodigo()) {
				$this->objPermissoes = $this->findManyToManyRowset(
					App_Model_DAO_Sistema_Acoes::getInstance(),
					App_Model_DAO_Sistema_Permissoes::getInstance(),
					'Perfil',
					'Acao'
				);
			} else {
				$this->objPermissoes = App_Model_DAO_Sistema_Acoes::getInstance()->createRowset();
			}
		}
		return $this->objPermissoes;
	}

	/**
	 * Verifica se o perfil tem permissão para a ação
	 * 
	 * @param App_Model_Entity_Sistema_Acao $acao
	 * @return boolean
	 */
	public function hasPermission(App_Model_Entity_Sistema_Acao $acao)
	{
		$retorno = false;
		foreach ($this->getPermissoes() as $perm) {
			if ($perm->getCodigo() === $acao->getCodigo()) {
				$retorno = true;
				break;
			}
		}
		return $retorno;
	}
}