<?php 
/* SVN FILE: $Id$ */
/* SecurityStatusesController Test cases generated on: 2009-08-26 12:08:21 : 1251278001*/
App::import('Controller', 'SecurityStatuses');

class TestSecurityStatuses extends SecurityStatusesController {
	var $autoRender = false;
}

class SecurityStatusesControllerTest extends CakeTestCase {
	var $SecurityStatuses = null;

	function setUp() {
		$this->SecurityStatuses = new TestSecurityStatuses();
		$this->SecurityStatuses->constructClasses();
	}

	function testSecurityStatusesControllerInstance() {
		$this->assertTrue(is_a($this->SecurityStatuses, 'SecurityStatusesController'));
	}

	function tearDown() {
		unset($this->SecurityStatuses);
	}
}
?>