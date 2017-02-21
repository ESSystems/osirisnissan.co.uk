<?php

$data = Set::flatten($attendance);

//foreach (array('Attendance', 'Person', 'Diagnosis') as $model) {
//	foreach ($attendance[$model] as $n=>$v) {
//		$data["{$model}.{$n}"] = $v;
//	}
//}

foreach (
	array(
		'work_related_absence',
		'work_discomfort',
		'review_attendance',
		'accident_report_complete',
		'no_work_contact',
	) as $n) {
		$data["Attendance.{$n}"] = intval($data["Attendance.{$n}"] == 'Y');
}

$json = array(
	'success'=>true,
	'data'=>$data
);

echo $javascript->object($json)
?>