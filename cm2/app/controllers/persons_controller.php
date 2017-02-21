<?php

class PersonsController extends AppController
{
	var $name = 'Persons';
	var $paginate = array('limit' => 10, 'page' => 1);
	
	var $uses = array('Person', 'Attendance', 'Absence');
	
	/**
	 * Person model instance
	 *
	 * @var Person
	 */
	var $Person;
	
	function test($id) {
		$data = $this->Person->find('all',
			array(
				'contain' => array('Employee', 'Employee.Department', 'Employee.JobClass'),
				'conditions' => array(
					'Person.id'=>$id,
				),
			)
		);
		debug($data);
		exit;
	}

	function index() {
		$this->set('persons', $this->paginate('Person'));
	}

	function page() {

		$this->initPaging($this->params['named']);
		
		$filter = array();
		if (!empty($this->data)) {
			if (isset($this->data['Option'])) {
				$options = $this->data['Option'];
				unset($this->data['Option']);
			}
			$d = &$this->data['Person'];
			if (isset($d['full_name'])) {
				unset($d['full_name']);
			}
			if (!empty($d['date_of_birth'])) {
				$d['date_of_birth'] = $this->Person->toDate($d['date_of_birth'], 'past');
			}

			foreach ($this->data as $model=>$fields) {
				foreach ($fields as $n=>$v) {
					if (is_array($v)) {
						unset($fields[$n]);
					}
				}
				$this->data[$model] = Set::filter($fields);
			}

			$filter = $this->postConditions($this->data, 
				array(
					'Person.first_name'=>'LIKE',
					'Person.middle_name'=>'LIKE',
					'Person.last_name'=>'LIKE',
				)
			);
		}
		
//		debug($filter);
		
//		$filter[] = 'Patient.PersonId IS NOT NULL';
		if (!empty($options['skip_leavers'])) {
			$filter[] = 'Employee.employment_end_date IS NULL';
		}
		if (!empty($options['employees_only'])) {
//			$filter[] = 'Employee.id IS NOT NULL';
		}
		
		$filter[]['Person.first_name !='] = '';
		
		$this->paginate['Person'] = array(
			'contain'    => array('Employee', 'Patient.Organisation(OrganisationName)'),
			'conditions' => $filter,
			'order' => 'Person.first_name, Person.last_name',
//			'limit' => 2,
		);
		
//		Configure::write('debug', 2);
//		debug($this->passedArgs);
//		exit;
		
		$people = $this->paginate('Person');
//		exit;
		
//		Configure::write('debug', 2);
//		debug($people);
//		exit;
		
		$totalPeople = $this->params['paging']['Person']['count'];
		
		$this->set('people', $people);
		$this->set('totalPeople', $totalPeople);
	}
	
	function alldup() {
		$this->initPaging();
		
		unset($this->passedArgs['order']);
		
		$this->paginate['Person'] = array(
			'contain' => array('Patient.Organisation'),
			'group' => array('Person.first_name', 'Person.last_name', 'Person.last_name', 'Person.date_of_birth', 'Patient.ResponsibleOrganisationID HAVING `Count` > 1'),
			'fields' => array('*', 'COUNT(*) As `Count`'),
			'order' => array('Count DESC', 'Person.first_name', 'Person.last_name', 'Person.date_of_birth'),
		);
		
		$data = $this->paginate('Person');
		
		foreach ($data as $i=>$r) {
			$data[$i]['Count'] = $data[$i][0]['Count'];
			if (empty($data[$i]['Patient']['Organisation'])) {
				$data[$i]['Patient']['ResponsibleOrganisationID'] = 0;
				$data[$i]['Patient']['Organisation']['OrganisationName'] = '';
			}
		}
		
		if (!empty($this->params['direct'])) {
			return array(
				'success' => true,
				'data' => $data,
				'total' => $this->params['paging']['Person']['count'],
				'metaData' => array(
					'root' => 'data',
					'fields' => array(
						'Person.first_name', 
						'Person.last_name',
						array(
							'name'=>'Person.date_of_birth',
							'type' => 'date', 
							'dateFormat' => 'Y-m-d H:i:s'
						),
						array(
							'name' => 'Patient.ResponsibleOrganisationID'
						),
						array(
							'name' => 'Patient.Organisation.OrganisationName'
						),
						array(
							'name'=>'Count',
							'type' => 'int'
						)
					),
					'totalProperty' => 'total'
				)
			);
		}
		
		debug($data);
		exit;
	}
	
	function entriesdup() {
		unset($this->data['Patient.Organisation.OrganisationName']);
		unset($this->data['Count']);
		
		if (empty($this->data['Person.date_of_birth'])) {
			$this->data['Person.date_of_birth'] = null;
		}
		if (empty($this->data['Patient.ResponsibleOrganisationID'])) {
			$this->data['AND'][] = array(
				'OR' => array(
					'Patient.ResponsibleOrganisationID' => 0,
					'Patient.ResponsibleOrganisationID IS NULL'
				)
			);
			unset($this->data['Patient.ResponsibleOrganisationID']);
		}
		
		$data = $this->Person->find('all',
			array(
				'contain' => array('Employee.Supervisor', 'Employee.Department', 'Employee.Client', 'Patient'),
				'conditions' => $this->data,
				'order' => 'Person.id'
			)
		);
		
		$data = Set::combine($data, '/Person/id', '/');
		
		$attendanceData = $this->Attendance->find('all',
			array(
				'contain' => array(),
				'conditions' => array(
					'Attendance.person_id' => array_keys($data)
				),
				'group' => 'Attendance.person_id',
				'fields' => array('Attendance.person_id', 'COUNT(*) AS `AttendanceCount`')
			)
		);
		
		$attendanceData = Set::combine($attendanceData, '/Attendance/person_id', '/');
		
		$absenceData = $this->Absence->find('all',
			array(
				'contain' => array(),
				'conditions' => array(
					'Absence.person_id' => array_keys($data)
				),
				'group' => 'Absence.person_id',
				'fields' => array('Absence.person_id', 'COUNT(*) AS `AbsenceCount`')
			)
		);
		
		$absenceData = Set::combine($absenceData, '/Absence/person_id', '/');
		
		foreach ($data as $i=>$r) {
			$data[$i]['AttendanceCount'] = empty($attendanceData[$i][0]['AttendanceCount'])?'':$attendanceData[$i][0]['AttendanceCount'];
			$data[$i]['AbsenceCount'] = empty($absenceData[$i][0]['AbsenceCount'])?'':$absenceData[$i][0]['AbsenceCount'];
			if (empty($data[$i]['Employee']['Supervisor'])) {
				$data[$i]['Employee']['Supervisor'] = array('Person' => array('full_name'=>''));
			}
			if (empty($data[$i]['Employee']['Department'])) {
				$data[$i]['Employee']['Department'] = array('DepartmentDescription' => '');
			}
		}
		
		if (!empty($this->params['direct'])) {
			return array(
				'success' => true,
				'data' => array_values($data),
				'metaData' => array(
					'root' => 'data',
					'fields' => array(
						'Person.id', 
						'Person.title', 
						'Person.full_name', 
						'Person.address1', 
						'Person.address2', 
						'Person.address3', 
						'Person.county', 
						'Person.post_code', 
						'Person.area_code', 
						'Person.telephone_number', 
						'Person.gender', 
						'Person.email_address', 
						array(
							'name'=>'Employee.employment_start_date',
							'type' => 'date', 
							'dateFormat' => 'Y-m-d H:i:s'
						),
						array(
							'name'=>'Employee.employment_end_date',
							'type' => 'date', 
							'dateFormat' => 'Y-m-d H:i:s'
						),
						'Employee.salary_number', 
						'Employee.Supervisor.Person.full_name', 
						'Employee.sap_number', 
						'Employee.Department.DepartmentDescription', 
						array(
							'name'=>'AttendanceCount',
						),
						array(
							'name'=>'AbsenceCount',
						),
					),
				)
			);
		}
		
		debug($data);
		exit;
	}
	
	

	function load($id) {
		$person = $this->Person->load($id);
		$this->set(compact('person'));
	}
	
	function save() {
		if (!empty($this->data)) {

			if (count(Set::filter($this->data['Employee'])) == 0) {
				unset($this->data['Employee']);
			}
			
			if ($this->Person->saveAll($this->data, array('validate'=>'first'))) {
				$this->set('status', 
					array(
						'success'=>true,
						'id' => $this->Person->id
					)
				);
			} else {
				$errors = array();
				foreach (array($this->Person, $this->Person->Patient) as $model) {
					$errors[$model->name] = $model->validationErrors;
				}
				$errors = Set::flatten($errors);
				$this->set('status', 
					array(
						'success'=>false,
						'errors' => $errors
					)
				);
			}
		}
	}
	
	function lookup() {
		$conditions = array();

		if(!isset($this->params['form']['showLeavers']) || isset($this->params['form']['showLeavers']) && $this->params['form']['showLeavers'] === 'false') {
			$conditions[]['OR'] = array(
				// 'Employee.employment_end_date' => '0000-00-00 00:00:00',
				'Employee.employment_end_date IS NULL',
				'Employee.employment_end_date > NOW()'
			);
		}
		
//		$conditions[]['AND'] = array(
//			'Person.first_name IS NOT NULL',
//			'Person.first_name !=' => '',
//			'Person.last_name IS NOT NULL',
//			'Person.last_name !=' => '',
//		);
		
		$query = trim(@$this->params['form']['query']);
		$contain = array(
			'Employee(id, person_id, sap_number, salary_number,employment_end_date)', 
			'Patient.Organisation(OrganisationName)'
		);
		
		if (!empty($query)) {
			$query = explode(' ', $query);
			
			// look for a number. If present, it will be treated as a salary or SAP number
			foreach ($query as $i=>$term) {
				$term = trim($term);
				if (is_numeric($term)) {
					$c = array(
						'Employee.salary_number LIKE' => "{$term}%"
					);
					if ($term{0} == '8') {
						// SAP number
						$c['Employee.sap_number LIKE'] = "{$term}%";
					}
					
					if (count($c) > 1) {
						$conditions[]['OR'] = $c;
					} else {
						$conditions[] = $c[0];
					}
					unset($query[$i]);
					$query = array_values($query);
					break;
				}
			}
			
			if (count($query) == 1) {
				$conditions[]['OR'] = array(
					'Person.first_name LIKE' => "{$query[0]}%",
					'Person.last_name LIKE' => "{$query[0]}%"
				);
			} elseif (count($query) >= 2) {
				$conditions[]['AND'] = array(
					'Person.first_name LIKE' => "{$query[0]}%",
					'Person.last_name LIKE' => "{$query[1]}%"
				);
			}
		}
		
		$this->paginate['Person'] = am(
			$this->paginate,
			array(
				'conditions' => $conditions,
				'contain' => $contain,
				'order'   => array('Person.first_name', 'Person.last_name'/*, 'Patient.ResponsibleOrganisationID', 'Employee.salary_number'*/),
				'fields' => array('Person.id', 'Person.first_name', 'Person.last_name', 'Person.date_of_birth')
			),
			Set::filter($this->initPaging())
		);
		
		$data = $this->paginate('Person');
		
		$this->set(compact('data'));
	}
	
	function form() {
	}

	function grid() {
	}

	function window() {
	}
	
	function get_pending_recalls($id) {
		$data = $this->Person->getPendingRecalls($id);
		
		if (!empty($this->params['requested'])) {
			return array(
				'success' => true,
				'data' => $data,
				'metaData' => array(
					'root' => 'data',
					'idProperty' => 'PendingEvent.id',
					'fields' => array(
						'RecallList.id', 'RecallList.title',
						'RecallListItem.id',
						'PendingEvent.id', 'PendingEvent.call_no', 'PendingEvent.recall_list_item_id', 
						array('name'=>'PendingEvent.recall_date', 'type'=>'date', 'dateFormat'=>'Y-m-d'),
					)
				)
			);
		}
		
		debug($data);
		exit;
	}
	
	
	function summary($id)
	{
	    $person = $this->Person->find('first',
	        array(
	            'contain' => array('Employee'),
	            'conditions' => array(
	                'Person.id' => $id
                )
            )
	    );

	    $this->set(compact('person'));
	}
	
}