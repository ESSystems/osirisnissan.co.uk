<?php 
/* SVN FILE: $Id$ */
/* ReferralsController Test cases generated on: 2011-09-01 13:09:47 : 1314873707*/
App::import('Controller', 'Referrals');

class TestReferrals extends ReferralsController {
	var $autoRender = false;
}

class ReferralsControllerTest extends CakeTestCase {
	var $Referrals = null;

	function setUp() {
		$this->Referrals = new TestReferrals();
		$this->Referrals->constructClasses();
	}

	function testReferralsControllerInstance() {
		$this->assertTrue(is_a($this->Referrals, 'ReferralsController'));
	}

	function tearDown() {
		unset($this->Referrals);
	}
}
?>