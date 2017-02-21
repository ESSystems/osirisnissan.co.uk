<?php 
/* SVN FILE: $Id$ */
/* RecallList Test cases generated on: 2009-04-09 16:04:40 : 1239283660*/
App::import('Model', 'RecallList');

class RecallListTestCase extends CakeTestCase {
	var $RecallList = null;
	var $fixtures = array('app.recall_list');

	function startTest() {
		$this->RecallList =& ClassRegistry::init('RecallList');
	}

	function testRecallListInstance() {
		$this->assertTrue(is_a($this->RecallList, 'RecallList'));
	}

	function testRecallListFind() {
		$this->RecallList->recursive = -1;
		$results = $this->RecallList->find('first');
		$this->assertTrue(!empty($results));

		$expected = array('RecallList' => array(
			'id'  => 1,
			'title'  => 'Lorem ipsum dolor sit amet',
			'recall_list_item_count'  => 1,
			'created'  => '2009-04-09 16:27:40',
			'modified'  => '2009-04-09 16:27:40'
		));
		$this->assertEqual($results, $expected);
	}
}
?>