<?php

class IndexController extends Zend_Controller_Action
{
	public function indexAction()
	{
		$this->view->HeadTitle('M�dulo Administrativo');
		$this->view->usuario = App_Plugin_Login::getInstance()->getIdentity();
	}
	//action de login
	public function loginAction()
	{
		$this->view->HeadTitle('Autentica��o');
		//verifica o post da tela de login
		if ($this->getRequest()->isPost())
		{
			//nao renderiza nenhuma view e seta o tipo de resposta como json
			$this->getFrontController()->setParam('noViewRenderer', true);
			$this->getResponse()->setHeader('Content-Type', 'text/json');
			//carrega as vcariaveis de login a partir do post
			$username = $this->getRequest()->getParam('usuario');
			$password = $this->getRequest()->getParam('senha');
			//cria uma instancia singleton do plugin de login
			$login = App_Plugin_Login::getInstance();
			if ($login->authenticate($username, $password))
			{
				$retorno = array(
					'success' => true,
					'data' => array(
						'codigo' => $login->getIdentity()->getCodigo(),
						'nome' => $login->getIdentity()->getNome()
					)
				);
			} else {
				$retorno = array(
					'success' => false,
					'message' => 'Usu�rio ou senha inv�lidos.'
				);
			}
			App_Funcoes_UTF8::encode($retorno);
			echo Zend_Json::encode($retorno);
		}
	}

	public function logoutAction()
	{
		$this->getFrontController()->setParam('noViewRenderer', true);
		$login = App_Plugin_Login::getInstance();
		if ($login->hasIdentity())
		{
			$login->clearIdentity();
			$this->getResponse()->setRedirect(Zend_Registry::get('config')->paths->admin->base);
		}
	}

	public function lostPasswordAction()
	{
		$this->getFrontController()->setParam('noViewRenderer', true);
		if (($login = $this->getRequest()->getParam('usuario')) != null) {
			try {
				$daoUsuarios = App_Model_DAO_Sistema_Usuarios::getInstance();
				$usuario = $daoUsuarios->fetchRow(
					$daoUsuarios->select()->where('usr_login = ?', $login)
				);
				if (null == $usuario) {
					throw new Exception('Usu�rio n�o encontrado');
				}

				try {
					$mail_template = new Zend_View();
					$mail_template->setBasePath(APPLICATION_PATH .'/views');
					$mail_template->usuario = $usuario;
					
					$mailTransport = new Zend_Mail_Transport_Smtp(Zend_Registry::get('config')->mail->host, Zend_Registry::get('config')->mail->smtp->toArray());
					$mail = new Zend_Mail();
					$mail->setFrom(Zend_Registry::get('config')->mail->webmaster, Zend_Registry::get('config')->project);
					$mail->addTo($usuario->getEmail());
					$mail->setSubject(Zend_Registry::get('config')->project.' - Recupera��o de senha');
					$mail->setBodyHtml($mail_template->render('index/lost-password.phtml'));
					$mail->send($mailTransport);
				} catch (Exception $e) {
					throw new Exception('Erro ao tentar enviar email. Por favor tente novamente mais tarde.');
				}

				$retorno = array(
					'success' => true,
					'message' => "<b>{$usuario->getNome()}</b> um e-mail foi enviado para <b><i>{$usuario->getEmail()}</i></b> contendo sua senha."
				);
			} catch (Exception $e) {
				$retorno = array(
					'success' => false,
					'message' => $e->getMessage()
				);
			}
			
			
			App_Funcoes_UTF8::encode($retorno);
			echo Zend_Json::encode($retorno);
		}
	}

	public function changePasswordAction()
	{
		if ($this->getRequest()->isPost()) {
			$this->getFrontController()->setParam('noViewRenderer', true);
			$this->getResponse()->setHeader('Content-Type', 'text/json');

			$retorno = array('success'=>false, 'message'=>'N�o foi poss�vel alterar sua senha', 'errors'=>array());
			$errors = array();
			$usuario = clone App_Plugin_Login::getInstance()->getIdentity();

			$senha_atual = $this->getRequest()->getParam('usr_senha');
			$nova_senha = $this->getRequest()->getParam('usr_senha_nova');
			$nova_senha_confirma = $this->getRequest()->getParam('usr_senha_confirma');

			if ($usuario->getSenha() != $senha_atual) {
				$errors[] = array('id'=>'usr_senha', 'msg'=>'A senha informada n�o confere');
			}
			if ($nova_senha != $nova_senha_confirma) {
				$errors[] = array('id'=>'usr_senha_confirma', 'msg'=>'A confirma��o n�o confere com a nova senha');
			}

			if (count($errors)) {
				$retorno['message'] = 'Por favor verifique os campos marcados em vermelho';
				$retorno['errors'] = $errors;
			} else {
				$usuario->setSenha($this->getRequest()->getParam('usr_senha_nova'));
				try {
					$usuario->save();
					App_Plugin_Login::getInstance()->setIdentity($usuario);

					$retorno['success'] = true;
					$retorno['message'] = 'Senha alterada com sucesso';
				} catch (Exception $e) {
					$usuario->setSenha($senha_atual);
					$retorno['success'] = false;
					$retorno['message'] = 'N�o foi poss�vel alterar sua senha';
				}
			}
			App_Funcoes_UTF8::encode($retorno);
			echo Zend_Json::encode($retorno);
		}
	}

	public function accessDeniedAction()
	{
		$this->view->HeadTitle('Acesso Negado');
	}

	public function keepAliveAction()
	{
		$this->getFrontController()->setParam('noViewRenderer', true);
		$this->getResponse()->setHeader('Content-Type', 'text/json');
		echo Zend_Json::encode(array('time' => time()));
	}
}