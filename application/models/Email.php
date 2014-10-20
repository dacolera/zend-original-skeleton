<?php

class App_Model_Email
{
	/*
	 *  @var Zend_Mail_Transport
	 */
	protected $mailTransport = null;
	
	public function __construct()
	{
		if(Zend_Registry::get('configInfo')->formaEnvio == 'SMTP') {
			$config = array(
				'auth' => 'login',
				'port' => Zend_Registry::get('configInfo')->porta,
				'username' => Zend_Registry::get('configInfo')->emailRemetente,
                'password' => Zend_Registry::get('configInfo')->senhaEmail
			);
			$this->mailTransport = new Zend_Mail_Transport_Smtp(Zend_Registry::get('configInfo')->servidor, $config);	
		} else {
			$this->mailTransport = new Zend_Mail_Transport_Sendmail();
		}
	}
	
	public function getFormaEnvio() {
		return $this->mailTransport;
	}
}