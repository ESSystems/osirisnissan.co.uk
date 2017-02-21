<?php 
/* SVN FILE: $Id$ */
/* DocumentsController Test cases generated on: 2011-10-29 16:10:15 : 1319896515*/
App::import('Controller', 'Documents');

class TestDocuments extends DocumentsController {
	var $autoRender = false;
}

class DocumentsControllerTest extends CakeTestCase {
	var $Documents = null;

	function setUp() {
		$this->Documents = new TestDocuments();
		$this->Documents->constructClasses();
	}

	function testDocumentsControllerInstance() {
		$this->assertTrue(is_a($this->Documents, 'DocumentsController'));
	}

	function tearDown() {
		unset($this->Documents);
	}
}
?>