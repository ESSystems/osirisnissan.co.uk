<?php

class EmployeeDepartment extends AppModel
{
	var $name       = 'EmployeeDepartment';
	var $useTable   = 'employee_department';
	var $primaryKey = 'person_id';
	
	var $belongsTo = array(
	    'Person'
    );
}