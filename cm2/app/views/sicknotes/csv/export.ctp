<?php 
	foreach ($data as $i=>$r) {
		$data[$i]['Absence']['work_related_absence'] = empty($data[$i]['Absence']['work_related_absence'])?'N':'Y'; 
		$data[$i]['Absence']['discomfort_report_completed'] = empty($data[$i]['Absence']['discomfort_report_completed'])?'N':'Y'; 
		$data[$i]['Absence']['accident_report_completed'] = empty($data[$i]['Absence']['accident_report_completed'])?'N':'Y'; 
		$data[$i]['Absence']['tickbox_neither'] = empty($data[$i]['Absence']['tickbox_neither'])?'N':'Y'; 
		$data[$i]['Sicknote']['start_date'] = $time->format('Y-m-d', $data[$i]['Sicknote']['start_date']);
		$data[$i]['Sicknote']['end_date'] = $time->format('Y-m-d', $data[$i]['Sicknote']['end_date']);
	}
	$csv->render($data, 
		array(
			'fields' => array(
				'Sicknote.id' => 'ID',
				'Absence.Person.full_name' => 'Employee',
				'Absence.Employee.salary_number' => 'Salary No',
				'Absence.Employee.sap_number' => 'SAP No',
				'Absence.Person.id' => 'Person ID',
				'SicknoteType.description' => 'Type',
				'Sicknote.start_date' => 'Start Date',
				'Sicknote.end_date' => 'End Date',
				'Sicknote.sick_days' => 'Sick Days',
				'Absence.work_related_absence' => 'Work Related',
				'Absence.discomfort_report_completed' => 'Work Discomfort',
				'Absence.accident_report_completed' => 'Accident Report',
				'Absence.tickbox_neither' => 'Neither',
				'Sicknote.symptoms_description' => 'Symptoms',
				'DiagnosesSicknote.Diagnosis.description' => 'Diagnosis',
				'Absence.Employee.Department.DepartmentDescription' => 'Department',
				'Absence.Person.Employee.Department.DepartmentDescription' => 'Cur.Department',
				'Absence.Employee.work_schedule_rule' => 'Work Schedule Rule',
				'Absence.Person.Employee.work_schedule_rule' => 'Cur. Work Schedule Rule',
				'Absence.Employee.JobClass.JobClassDescription' => 'Job Class',
				'Sicknote.comments' => 'Comments',
			)
		)
	);
?>