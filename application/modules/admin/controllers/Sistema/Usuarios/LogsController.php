<?php

class Sistema_Usuarios_LogsController extends Zend_Controller_Action
{
	public function init()
	{
		$this->getFrontController()->setParam('noViewRenderer', true);
		$this->getResponse()->setHeader('Content-Type', 'text/json');
	}

	public function indexAction()
	{
		switch ($this->getRequest()->getParam('load')) {
			case 'grid':
				$daoLogs = App_Model_DAO_Sistema_Usuarios_Logs::getInstance();
				$daoUsuarios = App_Model_DAO_Sistema_Usuarios::getInstance();
				
				$filter = $daoLogs->getAdapter()->select()
					->from($daoLogs->info('name'))
					->joinInner($daoUsuarios->info('name'), 'usr_log_idUsuario = usr_idUsuario')
					->limit($this->getRequest()->getParam('limit', 30), $this->getRequest()->getParam('start', 0))
					->order("{$this->getRequest()->getParam('sort', 'usr_log_data')} {$this->getRequest()->getParam('dir', 'DESC')}");
									
				App_Funcoes_Ext::FilterSQL($filter, $this->getRequest()->getParam('filter'));				

				if ($this->getRequest()->getParam('excel', false) == true) {
					$this->exportExcel($filter);
				} else {
					$retorno = array('logs' => array(), 'total' => 0);
					$rsLogs = $daoLogs->createRowset($daoLogs->getAdapter()->fetchAll($filter));
					foreach ($rsLogs as $log) {
						$frase = "Usuário {$log->getUsuario()->getNome()} ";
						$retorno['logs'][] = array(
							'usr_log_idLog' => $log->getCodigo(),
							'usr_log_modulo' => $log->getModulo(),
							'usr_log_acao' => $log->getAcao(),
							'usr_log_idUsuario' => $log->getUsuario()->getCodigo(),
							'usr_nome' => $log->getUsuario()->getNome(),
							'frase' => '',
							'usr_log_data' => $log->getData()
						);
					}
					$retorno['total'] = $daoLogs->getCount($filter);
					unset($rsLogs);

					App_Funcoes_UTF8::encode($retorno);
					echo Zend_Json::encode($retorno);
				}
				unset($daoLogs, $filter);
			break;
			case 'detalhe':
				if ($this->getRequest()->isPost()) {
					$retorno = array();
					$daoLogs = App_Model_DAO_Sistema_Usuarios_Logs::getInstance();
		
					$view = new Zend_View();
					$view->setBasePath(APPLICATION_PATH .DIRECTORY_SEPARATOR. 'views');
		
					try {
						$log = $daoLogs->fetchRow(
							$daoLogs->select()->where('usr_log_idLog = ?', $this->getRequest()->getParam('idLog'))
						);
						
						$view->dados = Zend_Json::decode($log->getParametros());
						$retorno = array('conteudo' => $view->render('sistema/usuarios/logs/detalhes-pedido.phtml'));
					} catch (Exception $e) {
						$retorno['message'] = $e->getMessage();
					}
		
					App_Funcoes_UTF8::encode($retorno);
					echo Zend_Json::encode($retorno);
				} else {
					$this->render('detalhe');
					$this->getResponse()->setHeader('Content-Type', 'text/javascript', true);
				}
			break;
			default:
				$this->getResponse()->setHeader('Content-Type', 'text/javascript', true);
				$this->getFrontController()->setParam('noViewRenderer', false);
		}
	}
	
	protected function exportExcel(Zend_Db_Select $filter)
	{
		require_once 'PHPExcel/IOFactory.php';
		
		$objReader = PHPExcel_IOFactory::createReader('Excel5');
		$objPHPExcel = $objReader->load(Zend_Registry::get('config')->paths->admin->file . "/xls/templatePadrao.xls");
		
		$styleTitulo = App_Funcoes_Excel::$styleTitulo;
		$styleUsuarioTitulo = App_Funcoes_Excel::$styleUsuarioTitulo;
		$styleHeader = App_Funcoes_Excel::$formatHeader;
		$styleDados = App_Funcoes_Excel::$formatDados;
		$styleDefault = App_Funcoes_Excel::$formatDefault;

		$objPHPExcel->getActiveSheet()->getDefaultStyle()->applyFromArray($styleDefault);
		$objPHPExcel->getActiveSheet()->setTitle('Logs');
		$objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(40);
		$objPHPExcel->getActiveSheet()->getRowDimension('23')->setRowHeight(25);
		
		$objPHPExcel->getActiveSheet()->setCellValue('A2', 'Listagem de Logs');
		$objPHPExcel->getActiveSheet()->getStyle('A2:G2')->applyFromArray($styleTitulo);
		
		$dadosUsuario = 'Usuário: ' . App_Plugin_Login::getInstance()->getIdentity()->getNome() . ' | Data: ' . date('d/m/Y') . ' | Horário: ' . date('H:i');
		$objPHPExcel->getActiveSheet()->setCellValue('A3', utf8_encode($dadosUsuario));
		$objPHPExcel->getActiveSheet()->getStyle('A3:G3')->applyFromArray($styleUsuarioTitulo);
		
		$objPHPExcel->getActiveSheet()->mergeCells('A2:E2');
		$objPHPExcel->getActiveSheet()->mergeCells('A3:E3');
		
		$objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(15);
		
		$objPHPExcel->getActiveSheet()->setCellValue('A5', utf8_encode('Código'));
		$objPHPExcel->getActiveSheet()->setCellValue('B5', utf8_encode('Usuário'));
		$objPHPExcel->getActiveSheet()->setCellValue('C5', 'Data');
		$objPHPExcel->getActiveSheet()->setCellValue('D5', utf8_encode('Módulo'));
		$objPHPExcel->getActiveSheet()->setCellValue('E5', utf8_encode('Ação'));
		
		//ajustando widths das colunas
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
		
		$objPHPExcel->getActiveSheet()->getStyle('A5:E5')->applyFromArray($styleHeader);
		$objPHPExcel->getActiveSheet()->setAutoFilter('A5:E5');
		
		$daoLogs = App_Model_DAO_Sistema_Usuarios_Logs::getInstance();
		$filter->limit(null, null);		
		$totalRegistros = $daoLogs->getCount(clone $filter);
		if ($totalRegistros > 0) {
			$linha = 6;
			for ($i = 0; $i < $totalRegistros; $i += 30) {
				$filter->limit(30, $i);				
				$rsRegistros = $daoLogs->getAdapter()->fetchAll($filter);				
				foreach ($rsRegistros as $arrRecord) {
					$record = $daoLogs->createRow($arrRecord);
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $linha, $record->getCodigo());
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $linha, $record->getUsuario()->getNome());
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $linha, App_Funcoes_Date::conversion($record->getData(), 0, 10));
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $linha, $record->getModulo());
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $linha, $record->getAcao());
										
					$objPHPExcel->getActiveSheet()->getStyle("A{$linha}:E{$linha}")->applyFromArray($styleDados);
					$linha++;
				}
				unset($rsRegistros);
			}
			$objPHPExcel->getActiveSheet()->getStyle("A{$linha}:E{$linha}")->applyFromArray($styleHeader);
		} else {
			$objPHPExcel->getActiveSheet()->setCellValue('A6', utf8_encode('Sem registros para exibição'));
			$objPHPExcel->getActiveSheet()->getStyle("A6:E6")->applyFromArray($styleDados);
			$objPHPExcel->getActiveSheet()->mergeCells('A6:E6');
			
			$objPHPExcel->getActiveSheet()->getStyle("A7:E7")->applyFromArray($styleHeader);
		}
		
		unset($daoBanners, $filter);
		
		// setando a planilha ativa
		$objPHPExcel->setActiveSheetIndex(0);
		
		// download do arquivo
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="Logs.xls"');
		header('Cache-Control: max-age=0');
		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
	}
}