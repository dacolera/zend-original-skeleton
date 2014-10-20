<?php

class Sistema_ConfiguracoesController extends Zend_Controller_Action {

	public function init()
	{
		$this->getFrontController()->setParam('noViewRenderer', true);
		$this->getResponse()->setHeader('Content-Type', 'text/json');
	}

	public function indexAction()
	{
		$daoConfiguracao = App_Model_DAO_Sistema_Configuracao::getInstance();
		$config = $daoConfiguracao->fetchRow($daoConfiguracao->select());
		if ($this->getRequest()->getParam('acao') == 'load') {
			$config = $config->toArray();
			
			$config['conf_emailFaleConosco'] = Zend_Json::decode(utf8_encode($config['conf_emailFaleConosco']));
			$config['conf_emailRepresentante'] = Zend_Json::decode(utf8_encode($config['conf_emailRepresentante']));
			$config['conf_emailMaisInformacoes'] = Zend_Json::decode(utf8_encode($config['conf_emailMaisInformacoes']));
			
			App_Funcoes_UTF8::decode($config['conf_emailFaleConosco']);
			App_Funcoes_UTF8::decode($config['conf_emailRepresentante']);
			App_Funcoes_UTF8::decode($config['conf_emailMaisInformacoes']);
			
			$retorno = array(
				'configuracoes' => array($config),
				'success' => true
			);

			App_Funcoes_UTF8::encode($retorno);
			echo Zend_Json::encode($retorno);

		} elseif ($this->getRequest()->getParam('acao') == 'update') {
			$daoImagens = App_Model_DAO_Galerias_Arquivos::getInstance();
			
			// Atualiza o XML com os dados postados
			try {
				$daoConfiguracao->getAdapter()->beginTransaction();
				$config
					->setEmailFaleConosco(utf8_decode($this->getRequest()->getParam('conf_emailFaleConosco')))
					->setEmailRepresentante(utf8_decode($this->getRequest()->getParam('conf_emailRepresentante')))
					->setEmailMaisInformacoes(utf8_decode($this->getRequest()->getParam('conf_emailMaisInformacoes')))
					->setPortaEmail($this->getRequest()->getParam('conf_portaEmail', 25))
					->setFormaEnvioEmail($this->getRequest()->getParam('conf_formaEnvioEmail', 'SMTP'))
					->setServidorEmail($this->getRequest()->getParam('conf_servidorEmail', ''))
					->setEmailRemetente($this->getRequest()->getParam('conf_emailRemetente', ''))
					->setNomeRemetente(utf8_decode($this->getRequest()->getParam('conf_nomeRemetente', '')))
					->setReplyTo($this->getRequest()->getParam('conf_replyTo', ''))
					->setSenhaEmail($this->getRequest()->getParam('conf_senhaEmail', ''));
				
				$config->save();
				
				$this->geraConfig($config);
				
				$daoConfiguracao->getAdapter()->commit();
				$retorno['success'] = true;
				$retorno['message'] = 'Configurações alteradas com sucesso.';
				$retorno['errors'] = array();
			} catch (Exception $e) {
				$daoConfiguracao->getAdapter()->rollBack();
				$retorno['success'] = false;
				$retorno['message'] = $e->getMessage();
				$retorno['errors'] = array();
			}

			App_Funcoes_UTF8::encode($retorno);
			echo Zend_Json::encode($retorno);
		} else {
			$this->getResponse()->setHeader('Content-Type', 'text/javascript', true);
			$this->getFrontController()->setParam('noViewRenderer', false);
		}
	}
	
	private function geraConfig($config)
	{
		$texto= '<?php
		
		return array(
			//configuração de e-mail
			"formaEnvio" => "'. $config->getFormaEnvioEmail() .'",
			"servidor" => "'. $config->getServidorEmail() .'",
			"porta" => "'. $config->getPortaEmail() .'",
			"emailRemetente" => "'. $config->getEmailRemetente() .'",
			"nomeRemetente" => "'. $config->getNomeRemetente() .'",
			"replyTo" => "'. $config->getReplyTo() .'",
			"senhaEmail" => "'. $config->getSenhaEmail() .'",
			
			//destinatarios de formularios
			"emailFaleConosco" => \''. $config->getEmailFaleConosco() .'\',
			"emailRepresentante" => \''. $config->getEmailRepresentante() .'\',
			"emailMaisInformacoes" => \''. $config->getEmailMaisInformacoes() .'\'
		);';
		
		$fp = fopen(APPLICATION_ROOT .'/configs/configuracao.php', "w");
		fwrite($fp, $texto);
		fclose($fp);
	}
}