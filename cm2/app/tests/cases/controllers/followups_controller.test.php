<?php 
/* SVN FILE: $Id$ */
/* FollowupsController Test cases generated on: 2009-06-10 10:06:07 : 1244619187*/
App::import('Controller', 'Followups');

class TestFollowups extends FollowupsController {
	var $autoRender = false;
}

class FollowupsControllerTest extends CakeTestCase {
	var $Followups = null;

	function setUp() {
		$this->Followups = new TestFollowups();
		$this->Followups->constructClasses();
	}

	function testFollowupsControllerInstance() {
		$this->assertTrue(is_a($this->Followups, 'FollowupsController'));
	}

	function tearDown() {
		unset($this->Followups);
	}
}
?>