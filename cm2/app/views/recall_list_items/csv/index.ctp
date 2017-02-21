<?php
$csv->render($data,
	array(
		'fields' => array(
			'Person.full_name' => 'Employee Name',
			'Patient.Organisation.OrganisationName' => 'Client',
		    'Employee.salary_number' => 'Salary No.',
			'Employee.sap_number' => 'SAP No.',
// 			'Employee.JobClass.JobClassDescription' => 'Job Class',
			'Employee.Supervisor.full_name' => 'Supervisor',
// 			'Person.Employee.Supervisor.full_name' => 'Current Supervisor',
			'Employee.Department.DepartmentDescription' => 'Department',
// 			'Person.Employee.Department.DepartmentDescription' => 'Current Department',
			'Employee.work_schedule_rule' => 'Work Schedule Rule',
// 			'Person.Employee.work_schedule_rule' => 'Current Work Schedule Rule',
			'RecallListItem.last_attended_date' => 'Last Test',
			'ZNextSchedule.recall_date' => 'Recall Date',
			'ZNextSchedule.due_date' => 'Due Date',
			'ZNextSchedule.contact_type' => 'Contact Type',
		)
	)
);
?>