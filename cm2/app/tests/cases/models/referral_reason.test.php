<?php 
/* SVN FILE: $Id$ */
/* ReferralReason Test cases generated on: 2011-09-01 13:09:21 : 1314873501*/
App::import('Model', 'ReferralReason');

class ReferralReasonTestCase extends CakeTestCase {
	var $ReferralReason = null;
	var $fixtures = array('app.referral_reason');

	function startTest() {
		$this->ReferralReason =& ClassRegistry::init('ReferralReason');
	}

	function testReferralReasonInstance() {
		$this->assertTrue(is_a($this->ReferralReason, 'ReferralReason'));
	}

	function testReferralReasonFind() {
		$this->ReferralReason->recursive = -1;
		$results = $this->ReferralReason->find('first');
		$this->assertTrue(!empty($results));

		$expected = array('ReferralReason' => array(
			'id'  => 1,
			'reason'  => 'Lorem ipsum dolor sit amet',
			'created_at'  => '2011-09-01 13:38:21',
			'updated_at'  => '2011-09-01 13:38:21'
		));
		$this->assertEqual($results, $expected);
	}
}
?>