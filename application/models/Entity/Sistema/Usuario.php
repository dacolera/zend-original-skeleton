<?php

/**
 * Definição d objeto Usuário
 *
 * @category 	Model
 * @package 	Model_Entity
 * @subpackage 	Sistema
 * @author 		Eduardo Schmidt <eduschmidt10@hotmail.com>
 */
class App_Model_Entity_Sistema_Usuario extends App_Model_Entity_Abstract
{
	/**
	 * @var App_Model_Entity_Sistema_Perfil
	 */
	protected $objPerfil = null;

	public function __sleep()
	{
		$fields = array_merge(parent::__sleep(), array('objPerfil'));
		return $fields;
	}

	public function __wakeup()
	{
		parent::__wakeup();
		$this->setTable(App_Model_DAO_Sistema_Usuarios::getInstance());
	}

	public function save()
	{
		$this->usr_status = (int) $this->usr_status;

		$filters = array(
			'*' => new Zend_Filter_StringTrim()
		);

		$validators = array(
			'usr_nome' => array(
				Zend_Filter_Input::ALLOW_EMPTY => false,
				new Zend_Validate_StringLength(4, 60)
			)
		);

		//verifica a consistência dos dados
		$this->validate($filters, $validators, $this->toArray());

		//persiste os dados no banco
		parent::save();
	}

	/**
	 * Recupera o código identificador do usuário
	 * 
	 * @return integer
	 */
	public function getCodigo()
	{
		return (int) $this->usr_idUsuario;
	}

	/**
	 * Define o perfil a qual o usuário pertence
	 * 
	 * @params App_Model_Entity_Sistema_Perfil $value
	 * @return App_Model_Entity_Sistema_Usuario
	 */
	public function setPerfil(App_Model_Entity_Sistema_Perfil $value)
	{
		$this->objPerfil = $value;
		$this->usr_idPerfil = $value->getCodigo();
		return $this;
	}

	/**
	 * Recupera o perfil a qual o usuário pertence
	 * 
	 * @return App_Model_Entity_Sistema_Perfil
	 */
	public function getPerfil()
	{
		if (null === $this->objPerfil && $this->getCodigo()) {
			$this->objPerfil = $this->findParentRow(App_Model_DAO_Sistema_Perfis::getInstance(), 'Perfil');
		}
		return $this->objPerfil;
	}

	/**
	 * Define o login de acesso do usuário
	 * 
	 * @param string $value
	 * @return App_Model_Entity_Sistema_Usuario
	 */
	public function setLogin($value)
	{
		$this->usr_login = (string) $value;
		return $this;
	}

	/**
	 * Recupera o login de acesso do usuário
	 * 
	 * @return string
	 */
	public function getLogin()
	{
		return (string) $this->usr_login;
	}

	/**
	 * Define a senha de acesso do usuário
	 * 
	 * @param string $value
	 * @return App_Model_Entity_Sistema_Usuario
	 */
	public function setSenha($value)
	{
		$this->usr_senha = (string) $value;
		return $this;
	}

	/**
	 * Recupera a senha de acesso do usuário
	 * 
	 * @return string
	 */
	public function getSenha()
	{
		return (string) $this->usr_senha;
	}

	/**
	 * Define o nome do usuário
	 * 
	 * @param string $value
	 * @return App_Model_Entity_Sistema_Usuario
	 */
	public function setNome($value)
	{
		$this->usr_nome = (string) $value;
		return $this;
	}

	/**
	 * Recupera o nome do usuário
	 * 
	 * @return string
	 */
	public function getNome()
	{
		return (string) $this->usr_nome;
	}

	/**
	 * Define o e-mail do usuário
	 * 
	 * @param string $value
	 * @return App_Model_Entity_Sistema_Usuario
	 */
	public function setEmail($value)
	{
		$this->usr_email = (string) $value;
		return $this;
	}

	/**
	 * Recupera o e-mail do usuário
	 * 
	 * @return string
	 */
	public function getEmail()
	{
		return (string) $this->usr_email;
	}

	/**
	 * Define se o usuário está ativo ou não
	 * 
	 * @param boolean $value
	 * @return App_Model_Entity_Sistema_Usuario
	 */
	public function setStatus($value)
	{
		$this->usr_status = (bool) $value;
	}

	/**
	 * Recupera se o usuário está ativo ou não
	 * @return boolean
	 */
	public function getStatus()
	{
		return (bool) $this->usr_status;
	}
}