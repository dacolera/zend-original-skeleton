<?php
		
		return array(
			//configura��o de e-mail
			"formaEnvio" => "SMTP",
			"servidor" => "smtp.cadastroweb.com.br",
			"porta" => "587",
			"emailRemetente" => "testedesenvolvimento@cadastroweb.com.br",
			"nomeRemetente" => "Teleatlantic",
			"replyTo" => "testedesenvolvimento@cadastroweb.com.br",
			"senhaEmail" => "teste123",
			
			//destinatarios de formularios
			"emailFaleConosco" => '[{"fale_id":1,"fale_departamento":"Comercial","fale_email":"faleconosco.comercial@teleatlantic.com.br"},{"fale_id":2,"fale_departamento":"Ouvidoria","fale_email":"faleconosco.ouvidoria@teleatlantic.com.br"},{"fale_id":3,"fale_departamento":"Cobran�a","fale_email":"faleconosco.cobranca@teleatlantic.com.br"},{"fale_id":4,"fale_departamento":"SAT","fale_email":"faleconosco.sat@teleatlantic.com.br"}]',
			"emailRepresentante" => '[{"representante_email":"solicitacao.representante@teleatlantic.com.br"}]',
			"emailMaisInformacoes" => '[{"maisinformacoes_id":1,"maisinformacoes_servicos":"Rastreamento de Ve�culos","maisinformacoes_email":"soliticacao.gps@teleatlantic.com.br"},{"maisinformacoes_id":2,"maisinformacoes_servicos":"Monitoramento de Alarme","maisinformacoes_email":"soliticacao.monitoria@teleatlantic.com.br"},{"maisinformacoes_id":3,"maisinformacoes_servicos":"Monitoramento de C�mera","maisinformacoes_email":"soliticacao.monitoria@teleatlantic.com.br"},{"maisinformacoes_id":4,"maisinformacoes_servicos":"Projeto Especial","maisinformacoes_email":"soliticacao.projetos@teleatlantic.com.br"}]'
		);