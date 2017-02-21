<?php 
/* SVN FILE: $Id$ */
/* Referral Test cases generated on: 2011-09-01 13:09:55 : 1314873475*/
App::import('Model', 'Referral');

class ReferralTestCase extends CakeTestCase {
	var $Referral = null;
	var $fixtures = array('app.referral');

	function startTest() {
		$this->Referral =& ClassRegistry::init('Referral');
	}

	function testReferralInstance() {
		$this->assertTrue(is_a($this->Referral, 'Referral'));
	}

	function testReferralFind() {
		$this->Referral->recursive = -1;
		$results = $this->Referral->find('first');
		$this->assertTrue(!empty($results));

		$expected = array('Referral' => array(
			'id'  => 1,
			'patient_id'  => 1,
			'patient_status_id'  => 1,
			'case_nature'  => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida,phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam,vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit,feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'job_information'  => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida,phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam,vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit,feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'history'  => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida,phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam,vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit,feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'referral_reason_id'  => 1,
			'created_at'  => '2011-09-01 13:37:55',
			'updated_at'  => '2011-09-01 13:37:55'
		));
		$this->assertEqual($results, $expected);
	}
}
?>