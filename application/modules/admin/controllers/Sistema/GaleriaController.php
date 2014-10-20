<?php

class Sistema_GaleriaController extends Zend_Controller_Action
{
	public function init()
	{
		$this->getFrontController()->setParam('noViewRenderer', true);
		$this->getResponse()->setHeader('Content-Type', 'text/json');
	}

	public function indexAction()
	{
		switch ($this->getRequest()->getParam('load', false)) {
			case 'galeria':
				$retorno = array();

				$daoGalerias = App_Model_DAO_Galerias::getInstance();

				$idGaleria = $this->getRequest()->getParam('node', false);
				if (false != $idGaleria) {
					if (is_numeric($idGaleria)) {
						$rsGalerias = $daoGalerias->fetchAll(
							$daoGalerias->select()->where('gal_idGaleriaPai = ?', $idGaleria)
						);
					} else {
						if (preg_match('/(root:)(?P<galeria>\w+)/', $idGaleria, $matches)) {
							$rsGalerias = new ArrayObject();
							$rsGalerias->append($daoGalerias->getGaleria($matches['galeria']));
						} else {
							$rsGalerias = $daoGalerias->getGaleria($idGaleria)->getFilhos();
						}
					}
				} else {
					$rsGalerias = $daoGalerias->getGalerias();
				}

				if ($rsGalerias) {
					foreach ($rsGalerias as $galeria) {
						$retorno[] = array(
							'id' => $galeria->getCodigo(),
							'text' => $galeria->getNome(),
							'iconCls' => 'icon-categorias-small',
							'leaf' => true
						);
					}
				}
				unset($daoGalerias, $rsGalerias);

				App_Funcoes_UTF8::encode($retorno);
				echo Zend_Json::encode($retorno);
				break;

			case 'arquivos':
				$retorno = array('arquivos' => array());
				if (false != $this->getRequest()->getParam('galeria', false)) {
					$daoArquivos = App_Model_DAO_Galerias_Arquivos::getInstance();
					$filter = $daoArquivos->select()
						->where('gal_arq_idGaleria = ?', $this->getRequest()->getParam('galeria'))
						->limit($this->getRequest()->getParam('limit', 30), $this->getRequest()->getParam('start', 0));
					
					if(($nome = $this->getRequest()->getParam('nome', false)) != false) {
						$filter->where('gal_arq_nome LIKE ?', "%{$nome}%");
					}
						
					$rsArquivos = $daoArquivos->fetchAll($filter);
					
					foreach ($rsArquivos as $arquivo) {
						$retorno['arquivos'][] = array(
							'gal_arq_idArquivo' => $arquivo->getCodigo(),
							'gal_arq_nome' => substr($arquivo->getNome(), 0, strrpos($arquivo->getNome(), '.')),
							'gal_arq_base' => $arquivo->getBasePath(),
							'gal_arq_info' => sprintf("<b>Data:</b> %s", App_Funcoes_Date::conversion($arquivo->getData()))
						);
					}
					
					$retorno['total'] = $daoArquivos->getAdapter()->fetchOne(
						$daoArquivos->getAdapter()->select()
							->from(array('temp' => $filter->reset(Zend_Db_Select::LIMIT_COUNT)->reset(Zend_Db_Select::LIMIT_OFFSET)), 'COUNT(1)')
					);
					unset($daoArquivos, $rsArquivos);

					App_Funcoes_UTF8::encode($retorno);
					echo Zend_Json::encode($retorno);
				}
			break;

			default:
				$this->getResponse()->setHeader('Content-Type', 'text/javascript', true);
				$this->view->fileSize = (int) preg_replace('/[^0-9]/', '', ini_get('upload_max_filesize')) * 1024;
				$this->getFrontController()->setParam('noViewRenderer', false);
		}
	}

	public function insertGaleriaAction()
	{
		if ($this->getRequest()->isPost()) {
			$daoGalerias = App_Model_DAO_Galerias::getInstance();
			$galeria = $daoGalerias->createRow();
			$galeria->setNome(utf8_decode($this->getRequest()->getParam('gal_nome')));
			if (false != ($idPai = $this->getRequest()->getParam('gal_idGaleriaPai', false))) {
				if(is_numeric($idPai)) {
					$galeria->setPai(
						$daoGalerias->fetchRow($daoGalerias->select()->where('gal_idGaleria = ?', $idPai))
					);
				} else {
					$galeria->setPai($daoGalerias->getGaleria($idPai));
				}
			}

			$retorno = array('success' => false);
			try {
				$galeria->save();

				$retorno['success'] = true;
				$retorno['message'] = sprintf('Galeria <b>%s</b> cadastrada com sucesso.', $galeria->getNome());
				$retorno['gal_idGaleria'] = $galeria->getCodigo();
				$retorno['gal_nome'] = $galeria->getNome();
			} catch (Exception $e) {
				$retorno['success'] = false;
				$retorno['message'] = (('development' == APPLICATION_ENV || $e->getCode() == E_USER_NOTICE) ? $e->getMessage() : 'Não foi possível inserir a galeria.');
			}
			unset($daoGalerias);

			App_Funcoes_UTF8::encode($retorno);
			echo Zend_Json::encode($retorno);
		}
	}

	public function updateGaleriaAction()
	{
		if ($this->getRequest()->isPost()) {
			$daoGalerias = App_Model_DAO_Galerias::getInstance();
			$galeria = $daoGalerias->fetchRow(
				$daoGalerias->select()->where('gal_idGaleria = ?', $this->getRequest()->getParam('gal_idGaleria'))
			);
			if (null == $galeria) {
				throw new Exception('Galeria não encontrada');
			}

			$retorno = array('success' => false);
			try {
				$galeria->setNome(utf8_decode($this->getRequest()->getParam('gal_nome')));
				$galeria->save();

				$retorno['success'] = true;
				$retorno['message'] = sprintf('Galeria <b>%s</b> alterada com sucesso.', $galeria->getNome());
			} catch (Exception $e) {
				$retorno['success'] = false;
				$retorno['message'] = (('development' == APPLICATION_ENV) ? $e->getMessage() : 'Não foi possível alterar a galeria.');
			}
			unset($daoGalerias);

			App_Funcoes_UTF8::encode($retorno);
			echo Zend_Json::encode($retorno);
		}
	}

	public function deleteGaleriaAction()
	{
		$retorno = array('success' => false, 'message' => '');
		try {
			$daoGalerias = App_Model_DAO_Galerias::getInstance();
			$galeria = $daoGalerias->fetchRow(
				$daoGalerias->select()->where('gal_idGaleria = ?', $this->getRequest()->getParam('gal_idGaleria'))
			);
			if (null == $galeria) {
				throw new Exception('Galeria não encontrada');
			}
			try {
				$nome = $galeria->getNome();
				$galeria->delete();

				$retorno['success'] = true;
				$retorno['message'] = sprintf('Galeria <b>%s</b> removida com sucesso.', $nome);
			} catch (Exception $e) {
				throw new Exception('development' == APPLICATION_ENV ? $e->getMessage() : 'Não foi possível remover a galeria');
			}
		} catch (Exception $e) {
			$retorno['success'] = false;
			$retorno['message'] = $e->getMessage();
		}

		App_Funcoes_UTF8::encode($retorno);
		echo Zend_Json::encode($retorno);
	}

	public function updateArquivoAction()
	{
		$retorno = array('success' => array(), 'fail' => array());
		$daoArquivos = App_Model_DAO_Galerias_Arquivos::getInstance();
		$file = $daoArquivos->fetchRow(
			$daoArquivos->select()->where('gal_arq_idArquivo = ?', $this->getRequest()->getParam('gal_arq_idArquivo'))
		);
		if ($file != null) {
			$oldFile = clone $file;
			$ext = strtolower(substr($file->getNome(), strrpos($file->getNome(), '.')));

			$nome = App_Funcoes_SEO::toString((utf8_decode($this->getRequest()->getParam('gal_arq_nome'))));
			$file->setNome($nome.$ext);

			try {
				$this->getGaleriaPai($file)->updateFile($oldFile, $file);
				$file->save();
				$retorno['success'][] = true;
				$retorno['message'] = 'Arquivo alterado com sucesso.';
			} catch (Exception $e) {
				$retorno['fail'][] = array(
					'id' => $file->getCodigo(),
					'msg' => ('development' == APPLICATION_ENV ? $e->getMessage() : "Não foi possível renomear o arquivo: {$file->getNome()}")
				);
			}
		}

		unset($daoArquivos);
		App_Funcoes_UTF8::encode($retorno);
		echo Zend_Json::encode($retorno);
	}

	public function deleteArquivoAction()
	{
		$retorno = array('success' => array(), 'fail' => array());
		$daoArquivos = App_Model_DAO_Galerias_Arquivos::getInstance();
		$arquivos = Zend_Json::decode($this->getRequest()->getParam('arquivos', array()));
		foreach ($arquivos as $idArquivo) {
			$arquivo = $daoArquivos->fetchRow(
				$daoArquivos->getAdapter()->quoteInto('gal_arq_idArquivo = ?', $idArquivo)
			);
			if (null != $arquivo) {
				try {
					$idRegistro = $arquivo->getCodigo();
					$arquivo->delete();
					$retorno['success'][] = $idRegistro;
				} catch (Exception $e) {
					$retorno['fail'][] = array(
						'id' => $arquivo->getCodigo(),
						'msg' => ('development' == APPLICATION_ENV ? $e->getMessage() : "Não foi possível remover o arquivo: {$arquivo->getNome()}")
					);
				}
			}
		}
		unset($daoArquivos);
		App_Funcoes_UTF8::encode($retorno);
		echo Zend_Json::encode($retorno);
	}

	public function uploadAction()
	{
		$daoGalerias = App_Model_DAO_Galerias::getInstance();
		$daoArquivos = App_Model_DAO_Galerias_Arquivos::getInstance();

		try {
			// setando a galeria pai
			$imagem = $daoArquivos->createRow()
				->setData(date('Y-m-d H:i:s'));
			if (false != ($idGaleria = $this->getRequest()->getParam('idGaleria', false))) {
				if(is_numeric($idGaleria)) {
					/*$a = $daoGalerias->fetchRow($daoGalerias->select()->where('gal_idGaleria = ?', $idGaleria));
					print_r($a); die;*/
					$imagem->setGaleria(
						$daoGalerias->fetchRow($daoGalerias->select()->where('gal_idGaleria = ?', $idGaleria))
					);
				} else {
					$imagem->setGaleria($daoGalerias->getGaleria($idGaleria));
				}
			}

			//recebe o arquivo
			try {
				$this->getGaleriaPai($imagem)->receive('Arquivos', $imagem);
			} catch (Exception $e) {
				throw new Exception(APPLICATION_ENV == 'development' ? $e->getMessage() : "Houve um problema ao receber o arquivo.");
			}

			$imagem->save();
			$retorno = array(
				'success' => true,
				'message' => 'OK'
			);
		} catch (Exception $e) {
			$retorno['fail'][] = array(
				'id' => $imagem->getCodigo(),
				'msg' => ('development' == APPLICATION_ENV ? $e->getMessage() : "Não foi possível cadastrar o arquivo.")
			);
		}
		App_Funcoes_UTF8::encode($retorno);
		print Zend_Json::encode($retorno);
		die();
	}

	private function getGaleriaPai($imagem = null, $galeria = null){
		($galeria == null ? $galeriaPai = $imagem->getGaleria() : $galeriaPai = $galeria);

		do {
			if($galeriaPai->getPai() != null) {
				$galeriaPai = $galeriaPai->getPai();
			}

		} while ($galeriaPai->getPai() != null);

		return $galeriaPai;
	}
}