<?php 
/* SVN FILE: $Id$ */
/* DeclinationsController Test cases generated on: 2011-10-28 10:10:47 : 1319787227*/
App::import('Controller', 'Declinations');

class TestDeclinations extends DeclinationsController {
	var $autoRender = false;
}

class DeclinationsControllerTest extends CakeTestCase {
	var $Declinations = null;

	function setUp() {
		$this->Declinations = new TestDeclinations();
		$this->Declinations->constructClasses();
	}

	function testDeclinationsControllerInstance() {
		$this->assertTrue(is_a($this->Declinations, 'DeclinationsController'));
	}

	function tearDown() {
		unset($this->Declinations);
	}
}
?>