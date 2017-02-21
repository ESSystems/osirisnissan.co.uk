<?php 
/* SVN FILE: $Id$ */
/* Nemployee Test cases generated on: 2010-03-26 17:03:01 : 1269616021*/
App::import('Model', 'Nemployee');

class NemployeeTestCase extends CakeTestCase {
	var $Nemployee = null;
	var $fixtures = array('app.nemployee');

	function startTest() {
		$this->Nemployee =& ClassRegistry::init('Nemployee');
	}

	function testNemployeeInstance() {
		$this->assertTrue(is_a($this->Nemployee, 'Nemployee'));
	}

	function testNemployeeFind() {
		$this->Nemployee->recursive = -1;
		$results = $this->Nemployee->find('first');
		$this->assertTrue(!empty($results));

		$expected = array('Nemployee' => array(
			'id'  => 1,
			'person_id'  => 1,
			'client_id'  => 1,
			'salary_number'  => 1,
			'sap_number'  => 1,
			'supervisor_id'  => 1,
			'sup_salary_number'  => 1,
			'sup_sap_number'  => 1,
			'employment_start_date'  => '2010-03-26',
			'employment_end_date'  => '2010-03-26',
			'department_id'  => 'Lorem ipsum dolor sit amet',
			'current_department_code'  => 'Lorem ipsum dolor sit amet',
			'job_class_id'  => 'Lorem ',
			'created'  => '2010-03-26 17:07:01',
			'modified'  => '2010-03-26 17:07:01',
			'is_obsolete'  => 1
		));
		$this->assertEqual($results, $expected);
	}
}
?>