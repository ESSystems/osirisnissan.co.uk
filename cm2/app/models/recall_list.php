<?php
class RecallList extends AppModel 
{
	var $name = 'RecallList';
	
	var $hasMany = array(
		'RecallListItem'
	);
	
	/**
	 * RecallListItem model instance
	 *
	 * @var RecallListItem
	 */
	var $RecallListItem;
	
	/**
	 * Filter a list of person ids. Leave only those who are members of the list
	 *
	 * @param int $id RecallList.id
	 * @param array $employeeIds
	 */
	function filterPeopleIds($id, $personIds) {
		$personIds = $this->RecallListItem->find('all',
			array(
				'contain' => array(),
				'conditions' => array(
					'RecallListItem.recall_list_id' => $id,
					'RecallListItem.person_id' => $personIds,
				),
				'fields' => array('RecallListItem.person_id')
			)
		);
		
		return Set::extract($personIds, '/RecallListItem/person_id');
	}
	
	/**
	 * Filter a list of employee ids. Leave only those who are members of the list
	 *
	 * @param int $id RecallList.id
	 * @param array $employeeIds
	 */
	function filterEmployeeIds($id, $employeeIds) {
		$this->bind('FoundEmployees', 
			array(
				'type' => 'hasMany',
				'className' => 'RecallListItem',
				'conditions' => array(
					'FoundEmployees.employee_id' => $employeeIds
				)
			)
		);
		
		$foundEmployees = $this->find('first',
			array(
				'contain' => array('FoundEmployees(employee_id)'),
				'conditions' => array(
					'RecallList.id' => $id
				)
			)
		);
		
		if (empty($foundEmployees)) {
			$foundEmployees = array();
		}
		
		return Set::extract('/FoundEmployees/employee_id', $foundEmployees);
	}
}
?>