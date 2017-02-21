<?php
class RecallListItem extends AppModel 
{
	var $name = 'RecallListItem';

	var $belongsTo = array(
		'RecallList' => array(
			'counterCache' => true
		),
		'Employee' => array(
			'className' => 'Nemployee',
			'foreignKey' => 'employee_id',
			'conditions' => array(
//				'Employee.employment_end_date IS NULL'
			)
		),
		'Person' => array(
		),
	    'NextSchedule' => array(
	        'className' => 'RecallListItemEvent',
	        'foreignKey' => 'last_invite_id'
        ),
	    'PendingEvent' => array(
	        'className' => 'RecallListItemEvent',
	        'foreignKey' => 'last_invite_id',
			'conditions' => array(
				'PendingEvent.attended_date' => null
			)
	    ),
	);
	
	var $hasOne = array(
	    /*
		'NextSchedule' => array(
			'className' => 'RecallListItemEvent',
			'conditions' => array(
				'NextSchedule.id = (SELECT MAX(id) FROM recall_list_item_events WHERE recall_list_item_id = RecallListItem.id)',
				'NextSchedule.attended_date' => null,
			)
		),
		'PendingEvent' => array(
			'className' => 'RecallListItemEvent',
			'conditions' => array(
//				'PendingEvent.due_date > NOW()',
				'PendingEvent.id = (SELECT MAX(id) FROM recall_list_item_events WHERE recall_list_item_id = RecallListItem.id)',
				'PendingEvent.attended_date' => null
			)
		),
		*/
	);
	
	var $hasMany = array(
		'RecallListItemEvent',
	);
	
	/**
	 * @var RecallList
	 */
	var $RecallList;
	
	/**
	 * @var Nemployee
	 */
	var $Employee;
	
	/**
	 * @var RecallListItemEvent
	 */
	var $NextSchedule;
	
	/**
	 * @var RecallListItemEvent
	 */
	var $RecallListItemEvent;
	
	/**
	 * @var RecallListItemEvent
	 */
	var $PendingEvent;
	
	function addSchedule($id, $date) {
		$now = date('Y-m-d');
		
		if (false && $date < $now) {
			$this->RecallListItemEvent->invalidate('due_date', 'Only dates in the future, please');
			return false;
		}
		
		// Find most recent pending event
		$pendingEvent = $this->RecallListItemEvent->find('first',
			array(
				'contain' => array(),
				'conditions' => array(
					'recall_list_item_id' => $id,
					'attended_date' => null
				),
				'order' => 'due_date DESC'
			)
		);
		
		$event = array(
			'recall_list_item_id' => $id,
			'due_date' => $date
		);
		
		if ($pendingEvent) {
			if ($pendingEvent['RecallListItemEvent']['due_date'] < $now) {
				// Event is overdue - schedule new invitation
				$event['call_no'] = $pendingEvent['RecallListItemEvent']['call_no'] + 1;
			} else {
				// Event is pending - just update it with the new due.
				$event['id'] = $pendingEvent['RecallListItemEvent']['id'];
			}
		}

		$this->RecallListItemEvent->create();
		return $this->RecallListItemEvent->save($event);
	}
	
	function getHistory($id) {
		return $this->RecallListItemEvent->find('all', 
			array(
				'contain' => array(),
				'conditions' => array(
					'RecallListItemEvent.recall_list_item_id'=>$id,
// 					'RecallListItemEvent.attended_date' => null
				)
			)
		);
	}
	
	function findByPerson($listId, $personId) {
		if (!($data = $this->find('first',
			array(
				'contain' => array(),
				'conditions' => array(
					'recall_list_id' => $listId,
					'person_id' => $personId
				)
			)
		))) {
			return false; 
		}
		
		return $data[$this->alias];
	}
}
?>