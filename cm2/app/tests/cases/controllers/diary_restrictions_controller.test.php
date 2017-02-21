<?php 
/* SVN FILE: $Id$ */
/* DiaryRestrictionsController Test cases generated on: 2011-08-23 12:08:46 : 1314091786*/
App::import('Controller', 'DiaryRestrictions');

class TestDiaryRestrictions extends DiaryRestrictionsController {
	var $autoRender = false;
}

class DiaryRestrictionsControllerTest extends CakeTestCase {
	var $DiaryRestrictions = null;

	function setUp() {
		$this->DiaryRestrictions = new TestDiaryRestrictions();
		$this->DiaryRestrictions->constructClasses();
	}

	function testDiaryRestrictionsControllerInstance() {
		$this->assertTrue(is_a($this->DiaryRestrictions, 'DiaryRestrictionsController'));
	}

	function tearDown() {
		unset($this->DiaryRestrictions);
	}
}
?>