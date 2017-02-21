<?php
class SecurityStatusesController extends AppController 
{
	var $name = 'SecurityStatuses';

	var $uses = array('Status');
	
	function index() {
		$this->set('data', $this->Status->find('all'));
	}
}
?>