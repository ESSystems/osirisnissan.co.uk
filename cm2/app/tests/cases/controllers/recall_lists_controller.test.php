<?php 
/* SVN FILE: $Id$ */
/* RecallListsController Test cases generated on: 2009-04-09 16:04:19 : 1239283699*/
App::import('Controller', 'RecallLists');

class TestRecallLists extends RecallListsController {
	var $autoRender = false;
}

class RecallListsControllerTest extends CakeTestCase {
	var $RecallLists = null;

	function setUp() {
		$this->RecallLists = new TestRecallLists();
		$this->RecallLists->constructClasses();
	}

	function testRecallListsControllerInstance() {
		$this->assertTrue(is_a($this->RecallLists, 'RecallListsController'));
	}

	function tearDown() {
		unset($this->RecallLists);
	}
}
?>