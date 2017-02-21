<?php

$rows = array();

foreach ($absences as $a) {
	$row = array(
		'id' => $a['Absence']['id'],
		'person_id' => $a['Absence']['person_id'],
		'full_name' => $a['Person']['full_name'],
		'start_date' => $time->format('d/m/y', $a['Absence']['start_date']),
		'end_date' => $time->format('d/m/y', $a['Absence']['end_date']),
		'returned_to_work_date' => !empty($a['Absence']['returned_to_work_date'])?$time->format('d/m/y', @$a['Absence']['returned_to_work_date']):'',
		'sick_days' => $a['Absence']['sick_days'],
		'department_name' => @$a['Employee']['Department']['DepartmentDescription'],
		'main_diagnosis' => $a['MainDiagnosis']['description'],
		'person_id' => $a['Absence']['person_id']
	);
	$rows[] = $row;
}

$json = array(
	'success' => true,
	'totalRows' => $totalAbsences,
	'id' => 'id',
	'rows' => $rows
);

?>

<?=$javascript->object($json)?>