<?php

class Sistema_UsuariosController extends Zend_Controller_Action
{
	public function init()
	{
		$this->getFrontController()->setParam('noViewRenderer', true);
		$this->getResponse()->setHeader('Content-Type', 'text/json');
	}

	public function indexAction()
	{
		switch ($this->getRequest()->getParam('load')) {
			case 'usuarios':
				$retorno = array();
				$daoPerfis = App_Model_DAO_Sistema_Perfis::getInstance();
				$rsPerfis = $daoPerfis->fetchAll();

				foreach ($rsPerfis as $perfil) {
					//dados do perfil
					$item = array(
						'idPerfil' => $perfil->getCodigo(),
						'nome' => $perfil->getNome(),
						'leaf' => false,
						'iconCls' => 'task-folder',
						'children' => array()
					);
					//usuários do perfil
					foreach ($perfil->getUsuarios() as $usuario) {
						$item['children'][] = array(
							'idUsuario' => $usuario->getCodigo(),
							'nome' => $usuario->getNome(),
							'login' => $usuario->getLogin(),
							'email' => $usuario->getEmail(),
							'status' => App_Funcoes_Rotulos::$status[$usuario->getStatus()],
							'leaf' => true,
							'iconCls' => 'icon-perfiluser'
						);
					}
					$retorno[] = $item;
				}
				unset($daoPerfis, $rsPerfis);

				App_Funcoes_UTF8::encode($retorno);
				echo Zend_Json::encode($retorno);
				break;

			case 'permissoes':
				$retorno = array();
				$daoAcoes = App_Model_DAO_Sistema_Acoes::getInstance();
				$daoPerfis = App_Model_DAO_Sistema_Perfis::getInstance();
				$perfil = $daoPerfis->fetchRow(
					$daoPerfis->select()->where('per_idPerfil = ?', $this->getRequest()->getParam('idPerfil'))
				);
				foreach ($daoAcoes->fetchAll() as $acao) {
					$item = array(
						'codigo' => $acao->getCodigo(),
						'modulo' => $acao->getModulo()->getRotulo(),
						'acao' => $acao->getRotulo(),
						'permissao' => $perfil->hasPermission($acao)
					);
					$retorno['acoes'][] = $item;
				}
				unset($daoAcoes, $daoPerfis);
				App_Funcoes_UTF8::encode($retorno);
				echo Zend_Json::encode($retorno);
				break;

			default:
				$this->getResponse()->setHeader('Content-Type', 'text/javascript', true);
				$this->getFrontController()->setParam('noViewRenderer', false);
		}
	}

	public function perfilInsertAction()
	{
		if ($this->getRequest()->isPost()) {
			$retorno = array('success' => false, 'message' => null, 'errors' => array(), 'perfil' => array());
			$daoPerfis = App_Model_DAO_Sistema_Perfis::getInstance();
			try {
				$perfil = $daoPerfis->createRow();
				$perfil->setNome(utf8_decode($this->getRequest()->getParam('per_nome')));

				try {
					$perfil->save();

					$retorno['success'] = true;
					$retorno['message'] = sprintf('Perfil <b>%s</b> cadastrado com sucesso.', $perfil->getNome());
				} catch (App_Validate_Exception $e) {
					$retorno['errors'] = App_Funcoes_Ext::fieldErrors($e);
					throw new Exception('Por favor verifique os campos marcados em vermelho.');
				} catch (Exception $e) {
					throw new Exception('development' == APPLICATION_ENV ? $e->getMessage() : 'Não foi possível cadastrar o perfil.');
				}
			} catch (Exception $e) {
				$retorno['success'] = false;
				$retorno['message'] = $e->getMessage();
			}
			unset($daoPerfis, $perfil);

			App_Funcoes_UTF8::encode($retorno);
			echo Zend_Json::encode($retorno);
		} else {
			$this->getResponse()->setHeader('Content-Type', 'text/javascript', true);
			$this->render('form-perfil');
		}
	}

	public function perfilUpdateAction()
	{
		if (false != ($idRegistro = $this->getRequest()->getParam('per_idPerfil', false))) {
			$retorno = array('success' => false, 'message' => null, 'errors' => array(), 'perfil' => array());
			$daoPerfis = App_Model_DAO_Sistema_Perfis::getInstance();
			try {
				$perfil = $daoPerfis->fetchRow(
					$daoPerfis->select()->where('per_idPerfil = ?', $idRegistro)
				);
				if (null == $perfil) {
					throw new Exception('O perfil solicitada não foi encontrado.');
				}

				if ($this->getRequest()->getParam('load')) {
					$retorno['success'] = true;
					$retorno['perfil'] = array($perfil->toArray());
				} else {
					$perfil->setNome(utf8_decode($this->getRequest()->getParam('per_nome')));
					try {
						$perfil->save();

						$retorno['success'] = true;
						$retorno['message'] = sprintf('Perfil <b>%s</b> alterado com sucesso.', $perfil->getNome());
					} catch (App_Validate_Exception $e) {
						$retorno['errors'] = App_Funcoes_Ext::fieldErrors($e);
						throw new Exception('Por favor verifique os campos marcados em vermelho.');
					} catch (Exception $e) {
						throw new Exception('development' == APPLICATION_ENV ? $e->getMessage() : 'Não foi possível alterar o perfil.');
					}
				}
			} catch (Exception $e) {
				$retorno['success'] = false;
				$retorno['message'] = $e->getMessage();
			}
			unset($daoPerfis);

			App_Funcoes_UTF8::encode($retorno);
			echo Zend_Json::encode($retorno);
		} else {
			$this->getResponse()->setHeader('Content-Type', 'text/javascript', true);
			$this->render('form-perfil');
		}
	}

	public function perfilDeleteAction()
	{
		$retorno = array('success' => false, 'message' => null);
		$daoPerfis = App_Model_DAO_Sistema_Perfis::getInstance();
		try {
			$perfil = $daoPerfis->fetchRow(
				$daoPerfis->select()->where('per_idPerfil = ?', $this->getRequest()->getParam('idPerfil'))
			);
			if (null == $perfil) {
				throw new Exception('O perfil solicitado não foi encontrada.');
			}
			try {
				$nome = $perfil->getNome();
				$perfil->delete();

				$retorno['success'] = false;
				$retorno['message'] = sprintf('Perfil <b>%s</b> removido com sucesso.', $nome);
			} catch (Exception $e) {
				throw new Exception('development' == APPLICATION_ENV ? $e->getMessage() : 'Não foi possível remover o perfil.');
			}
		} catch (Exception $e) {
			$retorno['success'] = false;
			$retorno['message'] = $e->getMessage();
		}
		unset($daoPerfis, $perfil);

		App_Funcoes_UTF8::encode($retorno);
		echo Zend_Json::encode($retorno);
	}

	public function usuarioInsertAction()
	{
		if ($this->getRequest()->isPost()) {
			$retorno = array('success' => false, 'message' => null, 'errors' => array(), 'usuario' => array());
			$daoUsuarios = App_Model_DAO_Sistema_Usuarios::getInstance();
			$daoPerfis = App_Model_DAO_Sistema_Perfis::getInstance();
			try {
				$usuario = $daoUsuarios->createRow();
				$usuario->setPerfil(
						$daoPerfis->fetchRow(
							$daoPerfis->select()->where('per_idPerfil = ?', $this->getRequest()->getParam('usr_idPerfil'))
						)
					)
					->setLogin(utf8_decode($this->getRequest()->getParam('usr_login')))
					->setSenha(utf8_decode($this->getRequest()->getParam('usr_senha')))
					->setNome(utf8_decode($this->getRequest()->getParam('usr_nome')))
					->setEmail(utf8_decode($this->getRequest()->getParam('usr_email')))
					->setStatus($this->getRequest()->getParam('usr_status'));

				try {
					$usuario->save();

					$retorno['success'] = true;
					$retorno['message'] = sprintf('Usuário <b>%s</b> cadastrado com sucesso.', $usuario->getNome());
				} catch (App_Validate_Exception $e) {
					$retorno['errors'] = App_Funcoes_Ext::fieldErrors($e);
					throw new Exception('Por favor verifique os campos marcados em vermelho.');
				} catch (Exception $e) {
					throw new Exception('development' == APPLICATION_ENV ? $e->getMessage() : 'Não foi possível cadastrar o usuário.');
				}
			} catch (Exception $e) {
				$retorno['success'] = false;
				$retorno['message'] = $e->getMessage();
			}
			unset($daoUsuarios, $daoPerfis, $usuario);

			App_Funcoes_UTF8::encode($retorno);
			echo Zend_Json::encode($retorno);
		} else {
			$this->getResponse()->setHeader('Content-Type', 'text/javascript', true);
			$this->render('form-usuario');
		}
	}

	public function usuarioUpdateAction()
	{
		if (false != ($idRegistro = $this->getRequest()->getParam('usr_idUsuario', false))) {
			$retorno = array('success' => false, 'message' => null, 'errors' => array(), 'usuario' => array());
			$daoUsuarios = App_Model_DAO_Sistema_Usuarios::getInstance();
			$daoPerfis = App_Model_DAO_Sistema_Perfis::getInstance();
			try {
				$usuario = $daoUsuarios->fetchRow(
					$daoUsuarios->select()->where('usr_idUsuario = ?', $idRegistro)
				);
				if (null == $usuario) {
					throw new Exception('O usuário solicitada não foi encontrado.');
				}

				if ($this->getRequest()->getParam('load')) {
					$retorno['success'] = true;
					$retorno['usuario'] = array($usuario->toArray());
				} else {
					$usuario->setPerfil(
							$daoPerfis->fetchRow(
								$daoPerfis->select()->where('per_idPerfil = ?', $this->getRequest()->getParam('usr_idPerfil'))
							)
						)
						->setLogin(utf8_decode($this->getRequest()->getParam('usr_login')))
						->setSenha(utf8_decode($this->getRequest()->getParam('usr_senha')))
						->setNome(utf8_decode($this->getRequest()->getParam('usr_nome')))
						->setEmail(utf8_decode($this->getRequest()->getParam('usr_email')))
						->setStatus($this->getRequest()->getParam('usr_status'));

					try {
						$usuario->save();

						$retorno['success'] = true;
						$retorno['message'] = sprintf('Usuário <b>%s</b> alterado com sucesso.', $usuario->getNome());
					} catch (App_Validate_Exception $e) {
						$retorno['errors'] = App_Funcoes_Ext::fieldErrors($e);
						throw new Exception('Por favor verifique os campos marcados em vermelho.');
					} catch (Exception $e) {
						throw new Exception('development' == APPLICATION_ENV ? $e->getMessage() : 'Não foi possível alterar o usuário.');
					}
				}
			} catch (Exception $e) {
				$retorno['success'] = false;
				$retorno['message'] = $e->getMessage();
			}
			unset($daoUsuarios, $daoPerfis);

			App_Funcoes_UTF8::encode($retorno);
			echo Zend_Json::encode($retorno);
		} else {
			$this->getResponse()->setHeader('Content-Type', 'text/', true);
			$this->render('form-usuario');
		}
	}

	public function usuarioDeleteAction()
	{
		$retorno = array('success' => false, 'message' => null);
		$daoUsuarios = App_Model_DAO_Sistema_Usuarios::getInstance();
		try {
			$usuario = $daoUsuarios->fetchRow(
				$daoUsuarios->select()->where('usr_idUsuario = ?', $this->getRequest()->getParam('idUsuario'))
			);
			if (null == $usuario) {
				throw new Exception('O usuário solicitado não foi encontrada.');
			}
			try {
				$nome = $usuario->getNome();
				$usuario->delete();

				$retorno['success'] = true;
				$retorno['message'] = sprintf('Usuário <b>%s</b> removido com sucesso.', $nome);
			} catch (Exception $e) {
				throw new Exception('development' == APPLICATION_ENV ? $e->getMessage() : 'Não foi possível remover o usuário.');
			}
		} catch (Exception $e) {
			$retorno['success'] = false;
			$retorno['message'] = $e->getMessage();
		}
		unset($daoUsuarios, $usuario);

		App_Funcoes_UTF8::encode($retorno);
		echo Zend_Json::encode($retorno);
	}

	public function perfilGrantPermsAction()
	{
		$retorno = array('success' => false, 'message' => null);

		$daoPerfis = App_Model_DAO_Sistema_Perfis::getInstance();
		$daoAcoes = App_Model_DAO_Sistema_Acoes::getInstance();

		$perfil = $daoPerfis->fetchRow(
			$daoPerfis->select()->where('per_idPerfil = ?', $this->getRequest()->getParam('idPerfil'))
		);
		$perms = explode(',', $this->getRequest()->getParam('perms', array()));
		$perfil->getPermissoes()->offsetRemoveAll();
		foreach ($perms as $idAcao) {
			$perfil->getPermissoes()->offsetAdd(
				$daoAcoes->fetchRow(
					$daoAcoes->select()->where('mod_acao_idAcao = ?', $idAcao)
				)
			);
		}
		try {
			$perfil->save();

			$retorno['success'] = true;
			$retorno['message'] = sprintf('Permissões do perfil <b>%s</b> salvas com sucesso.', $perfil->getNome());
		} catch (Exception $e) {
			$retorno['success'] = false;
			$retorno['message'] = ('development' == APPLICATION_ENV ? $e->getMessage() : 'Não foi possível remover o usuário.');
		}
		unset($daoPerfis, $daoAcoes, $perfil);

		App_Funcoes_UTF8::encode($retorno);
		echo Zend_Json::encode($retorno);
	}
}