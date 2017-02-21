<?php
$csv->render($data,
	array(
		'fields' => array(
			'Person.full_name' => 'Employee Name',
			'Employee.salary_number' => 'Salary No.',
			'Employee.sap_number' => 'SAP No.',
			'Employee.JobClass.JobClassDescription' => 'Job Class',
			'Employee.Supervisor.full_name' => 'Supervisor',
			'Person.Employee.Supervisor.full_name' => 'Current Supervisor',
			'Person.Patient.Organisation.OrganisationName' => 'Client',
			'Employee.Department.DepartmentDescription' => 'Department',
			'Person.Employee.Department.DepartmentDescription' => 'Current Department',
			'Employee.work_schedule_rule' => 'Work Schedule Rule',
			'Person.Employee.work_schedule_rule' => 'Current Work Schedule Rule',
			'RecallListItem.last_attended_date' => 'Last Test',
			'0.due_date' => 'Due Date',
			'NextSchedule.call_no' => 'Calls',
		)
	)
);
?>