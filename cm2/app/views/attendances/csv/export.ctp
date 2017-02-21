<?php 
	$csv->render($data, 
		array(
			'fields' => array(
				'Person.full_name' => 'Full Name',
				'Person.Patient.Organisation.OrganisationName' => 'Organisation',
				'Employee.Department.DepartmentDescription' => 'Department',
				'Person.Employee.Department.DepartmentDescription' => 'Current Department',
				'Employee.work_schedule_rule' => 'Work Schedule Rule',
				'Person.Employee.work_schedule_rule' => 'Current Work Schedule Rule',
				'Employee.JobClass.JobClassDescription' => 'Job Class',
				'Employee.salary_number' => 'Salary No',
				'Employee.sap_number' => 'SAP No',
				'Attendance.seen_at_time' => 'Seen at Date',
				'Attendance.attendance_date_time' => 'Attendance Date',
				'Clinic.clinic_name' => 'Clinic',
				'AttendanceReason.description' => 'Attendance Reason',
				'AttendanceResult.description' => 'Attendance Result',
				'Diagnosis.description' => 'Diagnosist',
				'Attendance.comments' => 'Comments',
				'User.Person.full_name' => 'Seen by',
				'Attendance.review_attendance' => 'Review',
				'Attendance.work_related_absence' => 'Work Related',
				'Attendance.accident_report_complete' => 'Accident Report',
				'Attendance.work_discomfort' => 'Work Discomfort',
				'Attendance.no_work_contact' => 'No Work Contact',
				'Attendance.person_id' => 'Person ID',
				'Person.Patient.Organisation.IsClient' => 'Is Client',
			)
		)
	);
?>