<?php
class RecallListsController extends AppController 
{
	var $name = 'RecallLists';
	
	/**
	 * RecallList model instance
	 *
	 * @var RecallList
	 */
	var $RecallList;
	
	/**
	 * All recall lists
	 *
	 */
	function index() {
		$this->paginate['RecallList'] = am(
			$this->paginate,
			array(
				'conditions' => array(
				),
				'contain' => array()
			),
			Set::filter($this->initPaging())
		);
		
		$data = $this->paginate('RecallList');
		
		$this->set(compact('data'));
	}
	
	function add() {
		if (!empty($this->data)) {
			$this->RecallList->create($this->data);
			if ($this->RecallList->save()) {
				$data = array(
					'success' => true
				); 
			} else {
				$data = array(
					'success' => false,
					'errors'  => Set::flatten($this->RecallList->validationErrors)
				);
			}
		} else {
			$data = array(
				'success' => false,
				'errors'  => 'Invalid request'
			);
		}
		
		$this->set(compact('data'));
	}
	
	function addItems() {
		extract($this->params['form']); // Extract $id and $peopleIds
		
		$foundPeopleIds = $this->RecallList->filterPeopleIds($id, $peopleIds);
		
		$peopleIds = array_diff($peopleIds, $foundPeopleIds);
		
		// Now $peopleIds contain only people who are not in the list.
		
		$data = array();
		
		if (!empty($peopleIds)) {
			// Convert peopleIds to employeeIds
			$employeeIds = $this->RecallList->RecallListItem->Person->find('all',
				array(
					'contain' => array('Employee'),
					'conditions' => array(
						'Person.id' => $peopleIds
					),
					'fields' => array('Employee.id', 'Person.id')
				)
			);

			foreach ($employeeIds as $r) {
				$data[] = array(
					'recall_list_id' => $id,
					'person_id'      => $r['Person']['id'],
					'employee_id'    => empty($r['Employee']['id'])?null:$r['Employee']['id'],
				);
			}
			
			$this->RecallList->RecallListItem->saveAll($data);
		}
		$this->set(compact('data'));
	}
	
	function delItems() {
		extract($this->params['form']); // Extract $id and $ids
				
		if (!empty($ids)) {
			$this->RecallList->RecallListItem->deleteAll(
				array(
					'RecallListItem.id' => $ids,
				), true, true
			);
		} else {
			$ids = array();
		}
		
		$this->set(compact('ids'));
	}
	
	function del($id) {
		$itemCount = $this->RecallList->field('recall_list_item_count', array('id'=>$id));
		if ($itemCount == 0) {
			$success = $this->RecallList->del($id);
			if (!$success) {
				$message = 'Unable to delete the list';
			} else {
				$message = 'List deleted';
			}
		} else {
			$success = false;
			$message = 'The list is not empty!';
		}
		
		$this->set('data', compact('success', 'message'));
	}
	
	function export($id) {
		$data = $this->RecallList->RecallListItem->find('all',
			array(
				'contain' => array(
					'NextSchedule' => array(
						'fields' => array(
							'CAST(due_date AS DATE) as due_date',
							'call_no'
						)
					), 
					'Person(first_name,last_name)', 
					'Person.Patient.Organisation(OrganisationName)', 
					'Employee.JobClass(JobClassDescription)', 
					'Employee.Supervisor(first_name,last_name)', 
					'Employee.Department(DepartmentDescription)',
					'Person.Employee.Department(DepartmentDescription)'
				),
				'conditions' => array(
					'RecallListItem.recall_list_id' => $id
				),
			)
		);
		
		$this->set(compact('data'));
	}
	
	function lookup() {
		$data = $this->RecallList->find('all',
			array(
				'contain' => array(),
				'fields' => array('RecallList.id', 'RecallList.title'),
				'order' => array('RecallList.title')
			)
		);
		
		return array(
			'success'=>true,
			'data' => $data,
			'metaData' => array(
				'root' => 'data',
				'idProperty' => 'RecallList.id',
				'fields' => array(
					array('name'=>'id', 'mapping'=>'RecallList.id'),
					array('name'=>'title', 'mapping'=>'RecallList.title'),
				)
			)
		);
	}
}
?>