<?php

class IndexController extends Zend_Controller_Action
{
	public $texto =  "Lorem Ipsum � simplesmente uma simula��o de texto da 
				ind�stria tipogr�fica e de impressos, e vem sendo 
				utilizado desde o s�culo XVI, quando um impressor 
				desconhecido pegou uma bandeja de tipos e os embaralhou 
				para fazer um livro de modelos de tipos. 
				Lorem Ipsum sobreviveu n�o s� a cinco s�culos, 
				como tamb�m ao salto para a editora��o eletr�nica, 
				permanecendo essencialmente inalterado. Se popularizou na d�cada de 60, 
				quando a Letraset lan�ou decalques contendo passagens de Lorem Ipsum, 
				e mais recentemente quando passou a ser integrado a softwares de editora��o eletr�nica como Aldus PageMaker.";
	
	public function indexAction(){
	
		$this->view->texto = $this->texto;
	}
	
	public function detalheAction(){
		
		$this->view->texto = $this->texto;
	}
}