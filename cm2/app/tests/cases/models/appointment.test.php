<?php 
/* SVN FILE: $Id$ */
/* Appointment Test cases generated on: 2011-06-29 08:06:30 : 1309327050*/
App::import('Model', 'Appointment');

class AppointmentTestCase extends CakeTestCase {
	var $Appointment = null;
	var $fixtures = array(
        'app.appointment', 'app.notification', 'app.user', 'app.person', 'app.patient',
	    'app.organisation', 'app.employee', 'app.client', 'app.recall_list_item',
	    'app.recall_list', 'app.recall_list_item_event', 'app.status', 'app.group', 'app.func',
	    'app.category', 'app.user_group', 'app.user_function', 'app.group_function',
	    'app.department', 'app.job_class', 'app.absence', 'app.diagnosis', 'app.sicknote', 
	    'app.sicknote_type', 'app.diagnoses_sicknote', 'app.attendance', 'app.attendance_reason', 
	    'app.attendance_result', 'app.clinic', 'app.followup', 'app.diary', 'app.diary_restriction', 
	    'app.referral', 'app.referral_reason', 'app.patient_status', 'app.referrer', 
	    'app.referrer_type', 'app.operational_priority', 'app.document', 'app.declination', 
	    'app.referrals_follower',
    );

	function startTest() {
		$this->Appointment =& ClassRegistry::init('Appointment');
	}

	function testAppointmentInstance() {
		$this->assertTrue(is_a($this->Appointment, 'Appointment'));
	}

	function testAppointmentFind() {
		$this->Appointment->recursive = -1;
		$results = $this->Appointment->find('first');
		$this->assertTrue(!empty($results));

		$expected = array('Appointment' => array(
			'id'  => 1,
			'user_id'  => 1,
			'calendar_id'  => 1,
			'person_id'  => 1,
			'from_date'  => '2011-06-29 08:57:30',
			'to_date'  => '2011-06-29 08:57:30',
			'note'  => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida,phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam,vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit,feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'created'  => '2011-06-29 08:57:30',
			'modified'  => '2011-06-29 08:57:30'
		));
		$this->assertEqual($results, $expected);
	}
}
?>