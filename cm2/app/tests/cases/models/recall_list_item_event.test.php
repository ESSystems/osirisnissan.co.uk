<?php 
/* SVN FILE: $Id$ */
/* RecallListItemEvent Test cases generated on: 2009-04-09 16:04:05 : 1239283685*/
App::import('Model', 'RecallListItemEvent');

class RecallListItemEventTestCase extends CakeTestCase {
	var $RecallListItemEvent = null;
	var $fixtures = array('app.recall_list_item_event');

	function startTest() {
		$this->RecallListItemEvent =& ClassRegistry::init('RecallListItemEvent');
	}

	function testRecallListItemEventInstance() {
		$this->assertTrue(is_a($this->RecallListItemEvent, 'RecallListItemEvent'));
	}

	function testRecallListItemEventFind() {
		$this->RecallListItemEvent->recursive = -1;
		$results = $this->RecallListItemEvent->find('first');
		$this->assertTrue(!empty($results));

		$expected = array('RecallListItemEvent' => array(
			'id'  => 1,
			'recall_list_item_id'  => 1,
			'note'  => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida,phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam,vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit,feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'created'  => '2009-04-09 16:28:05'
		));
		$this->assertEqual($results, $expected);
	}
}
?>