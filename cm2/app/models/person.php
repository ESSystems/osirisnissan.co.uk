<?php

class Person extends AppModel
{
	var $name     = 'Person';
	var $useTable = 'person';
	var $displayField = 'last_name';
	
	var $actsAs = array(
		'Containable',
		'Attribute' => array(
			'full_name'
		)
	);
	
	var $hasOne = array(
//		'Employee' => array(
//		), 
		'Patient' => array(
			'foreignKey' => 'PersonID'
		),
		'Employee' => array(
			'className' => 'Nemployee',
			'conditions' => array(
				'Employee.id = (SELECT MAX(id) FROM nemployees WHERE person_id = Person.id)'
			),
			'order' => 'Employee.id DESC'
		),
		'RecallListItem'
	);
	
//	var $belongsTo = array(
//		'EmployeeJobClass' => array(
//			'foreignKey' => 'id',
//			'order'      => 'EmployeeJobClass.from_date DESC'
//		),
//		'EmployeeDepartment' => array(
//			'foreignKey' => 'id',
//			'order'      => 'EmployeeDepartment.from_date DESC'
//		)
//	);
//
	var $validate = array(
//		'title' => array(
//			BLANK_ERROR => VALID_NOT_EMPTY
//		),
		'first_name' => array(
			BLANK_ERROR => VALID_NOT_EMPTY
		),
		'last_name' => array(
			BLANK_ERROR => VALID_NOT_EMPTY
		),
		'date_of_birth' => array(
			BLANK_ERROR => VALID_NOT_EMPTY
		),
		'organisation_id' => array(
			BLANK_ERROR => VALID_NOT_EMPTY
		),
	);
	
	/**
	 * @var Nemployee
	 */
	var $Employee;
	
	function beforeValidate() {
		$d = &$this->data['Person'];
		if (isset($d['date_of_birth'])) {
			$d['date_of_birth'] = $this->toDate($d['date_of_birth'], 'past');
		}
		
		return true;
	}
	
	function load($id) {
		$data = $this->find('first',
			array(
				'contain' => array(
					'Patient', 'Employee.Supervisor(first_name, last_name)', 'Employee.Supervisor.Person(email_address)', 'Employee.JobClass',
					'Employee.Department', 'Employee.Client'
				),
				'conditions' => array(
					'Person.id' => $id
				)
			)
		);
		
//		if (!empty($data['Employee']['Supervisor'])) {
//			$supervisor = $this->Employee->find('first',
//				array(
//					'contain' => array('Person(first_name, last_name)'),
//					'conditions' => array(
//						"TRIM(LEADING '0' FROM Employee.salary_number) = TRIM(LEADING '0' FROM '{$data['Employee']['Supervisor']}')"
//					)
//				)
//			);
//			if (is_array($supervisor['Employee'])) {
//				$data['Employee']['Supervisor'] = $supervisor['Employee'] + array('Person'=>$supervisor['Person']);
//			}
//		}
		
		return $data;
	}

	function afterFind($data, $primary = false) {
		if (!$primary) {
			$data = $this->Behaviors->Attribute->afterFind($this, $data, true);
		}

		return $data;
	}
	
	function full_name($row) {
		if (isset($row[$this->alias]['first_name'])) {
			return trim($row[$this->alias]['first_name'].' '. @$row[$this->alias]['last_name']);
		}
	}
	
	function paginateCount($conditions = null, $recursive = 0, $extra = array()) {
		$parameters = compact('conditions');
		$this->recursive = $recursive;
		$count = $this->find('count', array_merge($parameters, $extra));
		if (isset($extra['group'])) {
			$count = $this->getAffectedRows();
		}
		return $count;
	}
	
	function getCurrentEmployeeId($id) {
		$data = $this->find('first',
					array(
						'contain' => array('Employee'),
						'conditions' => array('Person.id'=>$id),
						'fields' => 'Employee.id'
					)
				);
		if ($data) {
			return $data['Employee']['id'];
		}
		
		return null;
	}
	
	function findEmployeeData($id, $options = array()) {
		$options += array(
			'contain' => array(),
			'conditions' => array(
				'Employee.person_id' => $id
			),
			'group' => 'Employee.person_id',
			'order' => 'Employee.id DESC',
		);
		
		return $this->Employee->find('all', $options);
	}
	
	function getPendingRecalls($id) {
		return $this->RecallListItem->find('all',
			array(
				'contain' => array('PendingEvent', 'RecallList'),
				'conditions' => array(
					'RecallListItem.person_id' => $id,
					'PendingEvent.id IS NOT NULL',
				),
				'order' => 'PendingEvent.recall_date, PendingEvent.id'
			)
		);
	}
}