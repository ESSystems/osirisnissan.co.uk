<?php 
/* SVN FILE: $Id$ */
/* RecallListItemsController Test cases generated on: 2009-04-13 15:04:09 : 1239624729*/
App::import('Controller', 'RecallListItems');

class TestRecallListItems extends RecallListItemsController {
	var $autoRender = false;
}

class RecallListItemsControllerTest extends CakeTestCase {
	var $RecallListItems = null;

	function setUp() {
		$this->RecallListItems = new TestRecallListItems();
		$this->RecallListItems->constructClasses();
	}

	function testRecallListItemsControllerInstance() {
		$this->assertTrue(is_a($this->RecallListItems, 'RecallListItemsController'));
	}

	function tearDown() {
		unset($this->RecallListItems);
	}
}
?>