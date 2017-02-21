<?php

class Employee extends AppModel 
{
	var $name       = 'Employee';
	var $useTable   = 'client_employee';
	var $primaryKey = 'person_id';
	
	var $actsAs = array(
		'Containable',
		'Attribute' => array(
			'leaver'
		)
	);
	
	var $belongsTo = array(
		'Person',
		'Client',
//		'Department' => array(
//			'foreignKey' => 'current_department_code'
//		),
		'EmployeeDepartment' => array(
			'order'      => 'EmployeeDepartment.from_date DESC'
		),
		'Supervisor' => array(
			'className' => 'employee',
			'foreignKey' => 'supervisor_id'
		),
	);
	
	var $hasAndBelongsToMany = array(
		'JobClass' => array(
			'with' => 'EmployeeJobClass',
			'foreignKey' => 'person_id',
			'associationForeignKey' => 'job_class_code',
			'order' => 'EmployeeJobClass.from_date, EmployeeJobClass.to_date',
		),
	);
	
	var $hasOne = array(
		'CurrentJobClass' => array(
			'className' => 'EmployeeJobClass',
			'foreignKey' => 'person_id',
			'conditions' => array(
				'CurrentJobClass.client_id = Employee.client_id'
			),
			'order' => 'CurrentJobClass.from_date DESC, CurrentJobClass.to_date DESC',
		),
		'Patient' => array(
			'foreignKey' => 'PersonID'
		),
	);
	
	function afterFind($data, $primary = false) {
		if (!$primary) {
			$data = $this->Behaviors->Attribute->afterFind($this, $data, true);
		}
		
		return $data;
	}
	
	function leaver($row) {
		if (empty($row['Employee']) || 
			empty($row['Employee']['employment_end_date']) ||
			'0000-00-00 00:00:00' == $row['Employee']['employment_end_date'] || 
			strtotime($row['Employee']['employment_end_date']) <= 0) {
			return '';
		}
		
		if ($row['Employee']['employment_end_date'] < date('Y-m-d H:i:s')) {
			return 'Yes ['.date('d/m/y', strtotime($row['Employee']['employment_end_date'])).']';
		}
		
		return '';
	}
}