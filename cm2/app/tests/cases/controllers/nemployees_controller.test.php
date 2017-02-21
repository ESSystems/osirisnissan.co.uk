<?php 
/* SVN FILE: $Id$ */
/* NemployeesController Test cases generated on: 2010-03-26 17:03:15 : 1269616035*/
App::import('Controller', 'Nemployees');

class TestNemployees extends NemployeesController {
	var $autoRender = false;
}

class NemployeesControllerTest extends CakeTestCase {
	var $Nemployees = null;

	function setUp() {
		$this->Nemployees = new TestNemployees();
		$this->Nemployees->constructClasses();
	}

	function testNemployeesControllerInstance() {
		$this->assertTrue(is_a($this->Nemployees, 'NemployeesController'));
	}

	function tearDown() {
		unset($this->Nemployees);
	}
}
?>