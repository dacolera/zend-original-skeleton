<?php

class IndexController extends Zend_Controller_Action
{
	
	public function indexAction(){
	
	}
	
	public function bootstrapElementsAction(){
		
	}
	
	public function formsAction(){
		
	}
	
	public function tablesAction(){
		
	}
	
	public function chartsAction(){
		
		$this->view->headScript()->appendFile("scripts/lib/plugins/flot/jquery.flot.js");
		$this->view->headScript()->appendFile("scripts/lib/plugins/flot/jquery.flot.tooltip.min.js");
		$this->view->headScript()->appendFile("scripts/lib/plugins/flot/jquery.flot.resize.js");
		$this->view->headScript()->appendFile("scripts/lib/plugins/flot/jquery.flot.pie.js");
		$this->view->headScript()->appendFile("scripts/lib/plugins/flot/flot-data.js");
		
	}
}