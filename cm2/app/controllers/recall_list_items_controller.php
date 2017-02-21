<?php
class RecallListItemsController extends AppController 
{
	var $name = 'RecallListItems';
	
	/**
	 * RecallListItem model instance
	 *
	 * @var RecallListItem
	 */
	var $RecallListItem;
	
	function index() {
//		if (empty($this->params['requested'])) {
//			Configure::write('debug', 2);
//		}
		$conditions = array(
			'OR' => array('Employee.id IS NULL', 'Employee.employment_end_date IS NULL') // Show only non-leavers in recall lists
		);
		
		$filter = empty($this->params['form']) ? $this->params['named'] : $this->params['form'];
		
		if (!empty($filter['recall_list_id'])) {
			$conditions['RecallListItem.recall_list_id'] = $filter['recall_list_id'];
		}
		if (!empty($filter['person_id'])) {
			$conditions['RecallListItem.person_id'] = $filter['person_id'];
		}
		if (!empty($filter['due_from'])) {
			$conditions['ZNextSchedule.recall_date >='] = $filter['due_from'];
		}
		if (!empty($filter['due_to'])) {
			$conditions['ZNextSchedule.recall_date <='] = $filter['due_to'];
		}

		$Person = $this->RecallListItem->Person;

		$Person->bindModel(
			array(
				'hasOne' => array(
					'ZNextSchedule' => array(
						'className' => 'RecallListItemEvent',
						'foreignKey' => false,
						'conditions' => array(
							'ZNextSchedule.id = RecallListItem.last_invite_id',
							'ZNextSchedule.attended_date' => null
						)
					),
				)
			),
			false
		);
		
		$this->paginate['Person'] = am(
			$this->paginate,
			array(
				'conditions' => $conditions,
				'contain' => array('RecallListItem', 'ZNextSchedule', 'Employee', 'Patient', /*'Patient.Organisation(OrganisationName)'*/),
				'fields'  => array(
					'RecallListItem.id', 'RecallListItem.last_attended_date', 'RecallListItem.modified',
					'Person.id', 'Person.first_name', 'Person.last_name', 
				    'ZNextSchedule.recall_date', 'ZNextSchedule.due_date', 'ZNextSchedule.call_no',
				    'ZNextSchedule.contact_type', 'ZNextSchedule.attended_date', 'ZNextSchedule.id',
					'Employee.id', 'Employee.department_id', 'Employee.supervisor_id',
					'Employee.salary_number', 'Employee.sap_number', 'Employee.work_schedule_rule', 
				    'Patient.ResponsibleOrganisationID'
				)
			),
			Set::filter($this->initPaging($filter))
		);
		
		$data = $this->paginate($Person);
		
		foreach ($data as $i=>$r) {
		    $employeeId = $r['Employee']['id'];
		    
			$employeeData = $Person->Employee->find('first',
				array(
					'contain' => array('Department(DepartmentDescription)', 'Supervisor(first_name,last_name)'),
					'conditions' => array('Employee.id' => $employeeId)
				)
			);
			$data[$i]['Employee']['Department']['DepartmentDescription'] = $employeeData['Department']['DepartmentDescription'];
			$data[$i]['Employee']['Supervisor']['full_name'] = $employeeData['Supervisor']['full_name'];

			$patientData = $Person->Patient->Organisation->find('first',
				array(
					'contain' => array(),
					'conditions' => array('OrganisationID' => $r['Patient']['ResponsibleOrganisationID']),
					'fields' => 'OrganisationName'
				)
			);
			if ($patientData) {
				$data[$i]['Patient'] += $patientData;
			}
		}
		
		$this->set(compact('data'));
		
//		if (empty($this->params['requested'])) {
//			debug($data);
//			exit;
//		}
	}
	
	function schedule($id, $date) {
		if ($this->RecallListItem->addSchedule($id, $date)) {
			return array('success'=>true);
		}
		
		return array(
			'success'=>false,
			'error' => Set::flatten($this->RecallListItem->RecallListItemEvent->validationErrors)
		);
	}
	
	function get_history($id) {
		$data = $this->RecallListItem->getHistory($id);
		
		$result = array(
			'success'=>true,
			'data' => $data,
			'metaData' => array(
				'root' => 'data',
				'idProperty' => 'RecallListItemEvent.id',
				'fields' => array(
					'RecallListItemEvent.id',
					array('name'=>'RecallListItemEvent.recall_list_item_id', 'type'=>'int'),
					'RecallListItemEvent.call_no',
					'RecallListItemEvent.contact_type',
					'RecallListItemEvent.comments',
					array('name'=>'RecallListItemEvent.created_by', 'type' => 'int'),
					array('name'=>'RecallListItemEvent.invite_date', 'type'=>'date', 'dateFormat' => 'Y-m-d'),
					array('name'=>'RecallListItemEvent.due_date', 'type'=>'date', 'dateFormat' => 'Y-m-d'),
					array('name'=>'RecallListItemEvent.recall_date', 'type'=>'date', 'dateFormat' => 'Y-m-d'),
					array('name'=>'RecallListItemEvent.attended_date', 'type'=>'date', 'dateFormat' => 'Y-m-d H:i:s'),
				)
			)
		);
		
//		debug($result);
//		exit;
		return $result;
	}
	
	function del() {
		$bStatus = $this->RecallListItem->RecallListItemEvent->del($this->data['RecallListItemEvent']['id']);
		return array(
			'success' => $bStatus
		);
	}
}
?>