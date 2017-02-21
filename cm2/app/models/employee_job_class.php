<?php

class EmployeeJobClass extends AppModel
{
	var $name       = 'EmployeeJobClass';
	var $useTable   = 'employee_job_class';
	var $primaryKey = 'person_id';
	
	var $belongsTo = array(
		'JobClass' => array(
			'foreignKey' => 'job_class_code',
		),
	    'Person',
	);
}