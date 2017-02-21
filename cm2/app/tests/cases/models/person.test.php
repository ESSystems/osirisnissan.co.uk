<?php 
/* SVN FILE: $Id$ */
/* Person Test cases generated on: 2011-06-29 08:06:30 : 1309327050*/
App::import('Model', 'Person');

class PersonTestCase extends CakeTestCase {
	var $Person = null;
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
		$this->Person =& ClassRegistry::init('Person');
	}

	function testPersonInstance() {
		$this->assertTrue(is_a($this->Person, 'Person'));
	}
}
?>