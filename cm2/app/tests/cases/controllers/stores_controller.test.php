<?php 
/* SVN FILE: $Id$ */
/* StoresController Test cases generated on: 2012-06-27 09:06:08 : 1340778608*/
App::import('Controller', 'Stores');

class TestStores extends StoresController {
	var $autoRender = false;
}

class StoresControllerTest extends CakeTestCase {
	var $Stores = null;

	function setUp() {
		$this->Stores = new TestStores();
		$this->Stores->constructClasses();
	}

	function testStoresControllerInstance() {
		$this->assertTrue(is_a($this->Stores, 'StoresController'));
	}

	function tearDown() {
		unset($this->Stores);
	}
}
?>