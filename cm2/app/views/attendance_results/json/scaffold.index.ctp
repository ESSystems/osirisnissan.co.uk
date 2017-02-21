<?php
$rows = array(
	array(
		'code' => '',
		'description' => '-',
	)
);
foreach ($attendanceResults as $r) {
	$rows[] = $r['AttendanceResult'];
}

$json = array(
	'success' => true,
	'rows'   => $rows
);
?>

<?=$javascript->object($json)?>