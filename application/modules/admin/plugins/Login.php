<?php

class App_Plugin_Login
{
	//instancia singleton de controle
	protected static $instance = null;
	//nome do storage
	protected $storageName = 'Teleatlantic_Login_Admin';
	//objeto de auth
	protected $auth;

	public function __construct()
	{
		//seta $auth como um objeto do tipostorage session com o nome $storageName
		$this->auth = Zend_Auth::getInstance()->setStorage(
			new Zend_Auth_Storage_Session($this->storageName)
		);
	}

	/**
	 * Implementa��o do m�todo Singleton para obter a instancia da classe
	 *
	 * @return App_Plugin_Login
	 */
	static public function getInstance()
	{
		if (null === self::$instance) {
			self::$instance = new self();
        }
		return self::$instance;
	}

	/**
	 * Autentica um usu�rio
	 *
	 * @param string $username
	 * @param string $password
	 * @return boolean
	 */
	public function authenticate($username, $password)
	{
		$authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table_Abstract::getDefaultAdapter());
		$authAdapter->setTableName('jur_sis_usuarios')
			->setIdentityColumn('usr_login')
			->setCredentialColumn('usr_senha')
			->setCredentialTreatment('? AND usr_status = 1');

		$authAdapter->setIdentity($username);
		$authAdapter->setCredential($password);

		$result = $this->auth->authenticate($authAdapter);
		if ($result->isValid()) {
			$data = $authAdapter->getResultRowObject('usr_idUsuario');

			$daoUsuarios = App_Model_DAO_Sistema_Usuarios::getInstance();
			$usuario = $daoUsuarios->fetchRow($daoUsuarios->getAdapter()->quoteInto('usr_idUsuario = ?', $data->usr_idUsuario));
			$this->setIdentity($usuario);
			unset($daoUsuarios, $usuario);
			return true;
		} else {
			return false;
		}
	}

	public function clearIdentity()
	{
		if ($this->auth->hasIdentity()) {
			$this->auth->clearIdentity();
		} else {
			throw new Exception('Nenhum usu�rio logado');
		}
	}

	public function setIdentity(App_Model_Entity_Sistema_Usuario $value)
	{
		$value->getPerfil()->getPermissoes(); //for�a o carregamento do perfil e das permiss�es para que fiquem salvas na sess�o
		$this->auth->getStorage()->write($value);
	}

	/**
	 * Recupera o usu�rio logado
	 *
	 * @return App_Model_Entity_Sistema_Usuario
	 * @throws Exception
	 */
	public function getIdentity()
	{
		if ($this->auth->hasIdentity()) {
			$identity = $this->auth->getStorage()->read();
			$identity->setTable(App_Model_DAO_Sistema_Usuarios::getInstance());
			return $identity;
		} else {
			throw new Exception('Nenhum usu�rio logado');
		}
	}

	/**
	 * Verifica se existe algum usu�rio logado
	 *
	 * @return boolean
	 */
	public function hasIdentity()
	{
		return $this->auth->hasIdentity();
	}
}