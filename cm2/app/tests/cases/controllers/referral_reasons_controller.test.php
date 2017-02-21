<?php 
/* SVN FILE: $Id$ */
/* ReferralReasonsController Test cases generated on: 2011-09-12 10:09:58 : 1315814158*/
App::import('Controller', 'ReferralReasons');

class TestReferralReasons extends ReferralReasonsController {
	var $autoRender = false;
}

class ReferralReasonsControllerTest extends CakeTestCase {
	var $ReferralReasons = null;

	function setUp() {
		$this->ReferralReasons = new TestReferralReasons();
		$this->ReferralReasons->constructClasses();
	}

	function testReferralReasonsControllerInstance() {
		$this->assertTrue(is_a($this->ReferralReasons, 'ReferralReasonsController'));
	}

	function tearDown() {
		unset($this->ReferralReasons);
	}
}
?>