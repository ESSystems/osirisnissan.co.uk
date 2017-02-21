<?php

$rows = array();

foreach ($sicknotes as $s) {
	$row = $s['Sicknote'];
	$row['start_date'] = (empty($row['start_date']))?'':$time->format('d/m/y', $row['start_date']);
	$row['end_date'] = (empty($row['start_date']))?'':$time->format('d/m/y', $row['end_date']);
	$row['created'] = (empty($row['created']))?'':stripZeroTime($time->format('d/m/y H:i', $row['created']));
	$rows[] = $row;
}

$json = array(
	'success' => true,
	'totalRows' => $totalSicknotes,
	'id' => 'id',
	'rows' => $rows
);

?>

<?=$javascript->object($json)?>