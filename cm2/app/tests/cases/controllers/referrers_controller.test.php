<?php 
/* SVN FILE: $Id$ */
/* ReferrersController Test cases generated on: 2011-10-20 11:10:47 : 1319099627*/
App::import('Controller', 'Referrers');

class TestReferrers extends ReferrersController {
	var $autoRender = false;
}

class ReferrersControllerTest extends CakeTestCase {
	var $Referrers = null;

	function setUp() {
		$this->Referrers = new TestReferrers();
		$this->Referrers->constructClasses();
	}

	function testReferrersControllerInstance() {
		$this->assertTrue(is_a($this->Referrers, 'ReferrersController'));
	}

	function tearDown() {
		unset($this->Referrers);
	}
}
?>