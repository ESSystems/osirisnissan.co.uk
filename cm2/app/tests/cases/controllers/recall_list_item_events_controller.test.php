<?php 
/* SVN FILE: $Id$ */
/* RecallListItemEventsController Test cases generated on: 2010-07-22 14:07:54 : 1279798194*/
App::import('Controller', 'RecallListItemEvents');

class TestRecallListItemEvents extends RecallListItemEventsController {
	var $autoRender = false;
}

class RecallListItemEventsControllerTest extends CakeTestCase {
	var $RecallListItemEvents = null;

	function setUp() {
		$this->RecallListItemEvents = new TestRecallListItemEvents();
		$this->RecallListItemEvents->constructClasses();
	}

	function testRecallListItemEventsControllerInstance() {
		$this->assertTrue(is_a($this->RecallListItemEvents, 'RecallListItemEventsController'));
	}

	function tearDown() {
		unset($this->RecallListItemEvents);
	}
}
?>