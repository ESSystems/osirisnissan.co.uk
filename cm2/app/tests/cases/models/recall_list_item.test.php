<?php 
/* SVN FILE: $Id$ */
/* RecallListItem Test cases generated on: 2009-04-09 16:04:56 : 1239283676*/
App::import('Model', 'RecallListItem');

class RecallListItemTestCase extends CakeTestCase {
	var $RecallListItem = null;
	var $fixtures = array('app.recall_list_item');

	function startTest() {
		$this->RecallListItem =& ClassRegistry::init('RecallListItem');
	}

	function testRecallListItemInstance() {
		$this->assertTrue(is_a($this->RecallListItem, 'RecallListItem'));
	}

	function testRecallListItemFind() {
		$this->RecallListItem->recursive = -1;
		$results = $this->RecallListItem->find('first');
		$this->assertTrue(!empty($results));

		$expected = array('RecallListItem' => array(
			'id'  => 1,
			'recall_list_id'  => 1,
			'employee_id'  => 1,
			'attended_on'  => '2009-04-09 16:27:56',
			'created'  => '2009-04-09 16:27:56',
			'modified'  => '2009-04-09 16:27:56'
		));
		$this->assertEqual($results, $expected);
	}
}
?>