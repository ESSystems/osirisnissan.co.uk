<?php 
/* SVN FILE: $Id$ */
/* PatientStatus Test cases generated on: 2011-09-01 13:09:06 : 1314873486*/
App::import('Model', 'PatientStatus');

class PatientStatusTestCase extends CakeTestCase {
	var $PatientStatus = null;
	var $fixtures = array('app.patient_status');

	function startTest() {
		$this->PatientStatus =& ClassRegistry::init('PatientStatus');
	}

	function testPatientStatusInstance() {
		$this->assertTrue(is_a($this->PatientStatus, 'PatientStatus'));
	}

	function testPatientStatusFind() {
		$this->PatientStatus->recursive = -1;
		$results = $this->PatientStatus->find('first');
		$this->assertTrue(!empty($results));

		$expected = array('PatientStatus' => array(
			'id'  => 1,
			'status'  => 'Lorem ipsum dolor sit amet',
			'created_at'  => '2011-09-01 13:38:06',
			'updated_at'  => '2011-09-01 13:38:06'
		));
		$this->assertEqual($results, $expected);
	}
}
?>