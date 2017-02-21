<?php 
/* SVN FILE: $Id$ */
/* AppointmentsController Test cases generated on: 2011-06-29 15:06:28 : 1309351228*/
App::import('Controller', 'Appointments');

class TestAppointments extends AppointmentsController {
	var $autoRender = false;
}

class AppointmentsControllerTest extends CakeTestCase {
	var $Appointments = null;

	function setUp() {
		$this->Appointments = new TestAppointments();
		$this->Appointments->constructClasses();
	}

	function testAppointmentsControllerInstance() {
		$this->assertTrue(is_a($this->Appointments, 'AppointmentsController'));
	}

	function tearDown() {
		unset($this->Appointments);
	}
}
?>