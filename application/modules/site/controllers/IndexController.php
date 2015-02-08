<?php

class IndexController extends Zend_Controller_Action
{
	
	public function indexAction()
	{

	}
	
	public function bootstrapElementsAction()
	{
		$this->view->active = "bootstrap";
	}
	
	public function formsAction()
	{
		$this->view->active = "form";
	}
	
	public function tablesAction()
	{
		$this->view->active = "tables";
	}
	
	public function chartsAction()
	{
		
		$this->view->headScript()->appendFile("scripts/lib/plugins/flot/jquery.flot.js");
		$this->view->headScript()->appendFile("scripts/lib/plugins/flot/jquery.flot.tooltip.min.js");
		$this->view->headScript()->appendFile("scripts/lib/plugins/flot/jquery.flot.resize.js");
		$this->view->headScript()->appendFile("scripts/lib/plugins/flot/jquery.flot.pie.js");
		$this->view->headScript()->appendFile("scripts/lib/plugins/flot/flot-data.js");

		$this->view->active = "charts";
		
	}

	public function hipotenuzaAction()
	{
		if ($this->getRequest()->isPost())
		{
			$lado1 = $this->getRequest()->getParams('lado1');
			$lado2 = $this->getRequest()->getParams('lado2');

			$this->view->app = (pow($lado1,2))+(pow($lado2,2));
			$this->view->msg = 'Clique no botão ao lado para tentar novamente';
		}
		$this->view->msg = "Insira a medida dos lados do triangulo";
	}
}