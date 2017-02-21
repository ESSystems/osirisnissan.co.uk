<?php
/*
$rows = array();

foreach ($attendances as $a) {
	$row = array(
		'id' => $a['Attendance']['id'],
		'person' => $a['Person']['full_name'],
		'clinic' => $a['Clinic']['clinic_name'],
		'reason' => $a['AttendanceReason']['description'],
		'result' => $a['AttendanceResult']['description'],
		'salary_number' => !empty($a['Person']['Employee']['salary_number'])?str_pad($a['Person']['Employee']['salary_number'], 10, '0', STR_PAD_LEFT):''
	);
	if (!empty($a['Attendance']['attendance_date_time'])) {
		$row['attendance_date_time'] = stripZeroTime(@$time->format('d/m/y H:i', @$a['Attendance']['attendance_date_time']));
	}
	if (!empty($a['Attendance']['seen_at_time'])) {
		$row['seen_at_time'] = stripZeroTime(@$time->format('d/m/y H:i', @$a['Attendance']['seen_at_time']));
	}
	
	$rows[] = $row;
}

$json = array(
	'success' => true,
	'totalRows' => $totalAttendances,
	'id' => 'id',
	'rows' => $rows
);
*/
?>

<?php
	/* 
	echo $javascript->object($json, 
		array(
			'stringKeys' => array('success'),
			'quoteKeys'  => false
		)
	)
	*/
?>

<?php 
	echo $javascript->Object(
		array(
			'success' => true,
			'totalRows' => $totalAttendances,
			'id' => 'Attendance.id',
			'rows' => $attendances,
		)
	)
?>