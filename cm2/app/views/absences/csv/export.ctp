<?php 
	foreach ($data as $i=>$r) {
		$data[$i]['Absence']['work_related_absence'] = empty($data[$i]['Absence']['work_related_absence'])?'N':'Y'; 
		$data[$i]['Absence']['discomfort_report_completed'] = empty($data[$i]['Absence']['discomfort_report_completed'])?'N':'Y'; 
		$data[$i]['Absence']['accident_report_completed'] = empty($data[$i]['Absence']['accident_report_completed'])?'N':'Y'; 
		$data[$i]['Absence']['tickbox_neither'] = empty($data[$i]['Absence']['tickbox_neither'])?'N':'Y'; 
		$data[$i]['Absence']['start_date'] = $time->format('Y-m-d', $data[$i]['Absence']['start_date']);
		$data[$i]['Absence']['end_date'] = $time->format('Y-m-d', $data[$i]['Absence']['end_date']);
	}
	$csv->render($data, 
		array(
			'fields' => array(
				'Absence.id' => 'ID',
				'Person.full_name' => 'Employee',
				'Person.id' => 'Person ID',
				'Employee.salary_number' => 'Salary No',
				'Employee.sap_number' => 'SAP No',
				'Employee.Supervisor.full_name' => 'Supervisor',
				'Absence.start_date' => 'Start Date',
				'Absence.end_date' => 'End Date',
				'Absence.returned_to_work_date' => 'Returned to Work',
				'Employee.Department.DepartmentDescription' => 'Department',
				'Person.Employee.Department.DepartmentDescription' => 'Current Department',
				'Employee.work_schedule_rule' => 'Work Schedule Rule',
				'Person.Employee.work_schedule_rule' => 'Current Work Schedule Rule',
				'Employee.JobClass.JobClassDescription' => 'Job Class',
				'Absence.sick_days' => 'Sick Days',
				'Absence.calc_sick_days' => 'Calc. Sick Days',
				'Absence.work_related_absence' => 'Work Related',
				'Absence.discomfort_report_completed' => 'Work Discomfort',
				'Absence.accident_report_completed' => 'Accident Report',
				'Absence.tickbox_neither' => 'Neither',
				'MainDiagnosis.description' => 'Main Diagnosis',
			)
		)
	);
?>