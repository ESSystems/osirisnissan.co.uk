<?php
	foreach ($data as $depId => $groupedAtts) {
		$groupedAtts = Set::combine(array_values($groupedAtts), '/Attendance/id', '/', '/Employee/supervisor_id');
		foreach ($groupedAtts as $supervisorId => $atts) {
			echo 'Department: ';
			echo !empty($departmentsMap[$depId])?$departmentsMap[$depId]:'n/a';
			echo ', Supervisor: ';
			echo !empty($supervisorsMap[$supervisorId])?$supervisorsMap[$supervisorId]:'n/a';
			echo "\n";
 
			$csv->render($atts, 
				array(
					'fields' => array(
						'Person.full_name' => 'Full Name',
						'Employee.salary_number' => 'Salary No',
						'Employee.sap_number' => 'SAP No',
						'Attendance.attendance_date_time' => 'Attendance Date',
						'SeenBy.full_name' => 'Appointment With',
						'AttendanceReason.description' => 'Reason',
						'AttendanceResult.description' => 'Result',
						'Diagnosis.description' => 'Diagnosis',
						'Attendance.comments' => 'Comments',
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
			echo "\n";
		}
	}
?>
