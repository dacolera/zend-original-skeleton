<?php

class App_Model_Entity_Sistema_Configuracao extends App_Model_Entity_Abstract
{
	public function __sleep()
	{
		$fields = array_merge(parent::__sleep(), array());
		return $fields;
	}

	public function __wakeup()
	{
		parent::__wakeup();
		$this->setTable(App_Model_DAO_Configuracao::getInstance());
	}

	public function save()
	{
		$filters = array(
			'*' => new Zend_Filter_StringTrim()
		);
		
		$validators = array(
			'conf_formaEnvioEmail' => array(
				Zend_Filter_Input::ALLOW_EMPTY => true
			),
			'conf_servidorEmail' => array(
				Zend_Filter_Input::ALLOW_EMPTY => true
			),
			'conf_emailRemetente' => array(
				Zend_Filter_Input::ALLOW_EMPTY => true
			),
			'conf_nomeRemetente' => array(
				Zend_Filter_Input::ALLOW_EMPTY => true
			),
			'conf_replyTo' => array(
				Zend_Filter_Input::ALLOW_EMPTY => true
			),
			'conf_senhaEmail' => array(
				Zend_Filter_Input::ALLOW_EMPTY => true
			)
		);

		//verifica a consistência dos dados
		$this->validate($filters, $validators, $this->toArray());

		//persiste os dados no banco
		parent::save();
	}

	public function getCodigo()
	{
		return $this->conf_idConfiguracao;
	}

	public function getEmailFaleConosco()
	{
		return (string) $this->conf_emailFaleConosco;
	}
	
	public function setEmailFaleConosco($value)
	{
		$this->conf_emailFaleConosco = (string) $value;
		return $this;
	}
	
	public function getEmailRepresentante()
	{
		return (string) $this->conf_emailRepresentante;
	}
	
	public function setEmailRepresentante($value)
	{
		$this->conf_emailRepresentante = (string) $value;
		return $this;
	}

	public function getEmailMaisInformacoes()
	{
		return (string) $this->conf_emailMaisInformacoes;
	}
	
	public function setEmailMaisInformacoes($value)
	{
		$this->conf_emailMaisInformacoes = (string) $value;
		return $this;
	}
	
	public function getFormaEnvioEmail()
	{
		return (string) $this->conf_formaEnvioEmail;
	}
	
	public function setFormaEnvioEmail($value)
	{
		$this->conf_formaEnvioEmail = (string) $value;
		return $this;
	}
	
	public function getPortaEmail()
	{
		return (int) $this->conf_portaEmail;
	}
	
	public function setPortaEmail($value)
	{
		$this->conf_portaEmail = (int) $value;
		return $this;
	}
	
	public function getServidorEmail()
	{
		return (string) $this->conf_servidorEmail;
	}
	
	public function setServidorEmail($value)
	{
		$this->conf_servidorEmail = (string) $value;
		return $this;
	}
	
	public function getEmailRemetente()
	{
		return (string) $this->conf_emailRemetente;
	}
	
	public function setEmailRemetente($value)
	{
		$this->conf_emailRemetente = (string) $value;
		return $this;
	}
	
	public function getNomeRemetente()
	{
		return (string) $this->conf_nomeRemetente;
	}
	
	public function setNomeRemetente($value)
	{
		$this->conf_nomeRemetente = (string) $value;
		return $this;
	}
	
	public function getReplyTo()
	{
		return (string) $this->conf_replyTo;
	}
	
	public function setReplyTo($value)
	{
		$this->conf_replyTo = (string) $value;
		return $this;
	}

	public function getSenhaEmail()
	{
		return (string) $this->conf_senhaEmail;
	}
	
	public function setSenhaEmail($value)
	{
		$this->conf_senhaEmail = (string) $value;
		return $this;
	}
}