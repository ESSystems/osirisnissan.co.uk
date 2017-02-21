<?php 
/* SVN FILE: $Id$ */
/* Nemployee Fixture generated on: 2010-03-26 17:03:01 : 1269616021*/

class NemployeeFixture extends CakeTestFixture {
	var $name = 'Nemployee';
	var $fields = array(
		'id' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'person_id' => array('type'=>'integer', 'null' => false, 'default' => NULL, 'key' => 'index'),
		'client_id' => array('type'=>'integer', 'null' => false, 'default' => NULL),
		'salary_number' => array('type'=>'integer', 'null' => true, 'default' => NULL),
		'sap_number' => array('type'=>'integer', 'null' => true, 'default' => NULL),
		'supervisor_id' => array('type'=>'integer', 'null' => true, 'default' => NULL),
		'sup_salary_number' => array('type'=>'integer', 'null' => true, 'default' => NULL),
		'sup_sap_number' => array('type'=>'integer', 'null' => true, 'default' => NULL),
		'employment_start_date' => array('type'=>'date', 'null' => true, 'default' => NULL),
		'employment_end_date' => array('type'=>'date', 'null' => true, 'default' => NULL),
		'department_id' => array('type'=>'string', 'null' => false, 'default' => NULL, 'length' => 32),
		'current_department_code' => array('type'=>'string', 'null' => true, 'default' => NULL, 'length' => 32),
		'job_class_id' => array('type'=>'string', 'null' => false, 'default' => NULL, 'length' => 8),
		'created' => array('type'=>'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type'=>'datetime', 'null' => true, 'default' => NULL),
		'is_obsolete' => array('type'=>'boolean', 'null' => false, 'default' => '0'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'person_id' => array('column' => 'person_id', 'unique' => 0))
	);
	var $records = array(array(
		'id'  => 1,
		'person_id'  => 1,
		'client_id'  => 1,
		'salary_number'  => 1,
		'sap_number'  => 1,
		'supervisor_id'  => 1,
		'sup_salary_number'  => 1,
		'sup_sap_number'  => 1,
		'employment_start_date'  => '2010-03-26',
		'employment_end_date'  => '2010-03-26',
		'department_id'  => 'Lorem ipsum dolor sit amet',
		'current_department_code'  => 'Lorem ipsum dolor sit amet',
		'job_class_id'  => 'Lorem ',
		'created'  => '2010-03-26 17:07:01',
		'modified'  => '2010-03-26 17:07:01',
		'is_obsolete'  => 1
	));
}
?>