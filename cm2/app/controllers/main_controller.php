<?php

class MainController extends AppController 
{
	var $uses = array();
	var $layout = 'main';

	function index() {
		
	}
	
	function extjs() {
		$this->redirect('/main/index.extjs', null, true);
	}
}