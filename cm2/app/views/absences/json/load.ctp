<?php

echo $javascript->Object(
	array(
		'success'=>true,
		'data'=>Set::flatten($absence)
	)
);
return;

$data = array();

foreach (array('Absence', 'Person', 'MainDiagnosis') as $model) {
	foreach ($absence[$model] as $n=>$v) {
		$data["{$model}.{$n}"] = $v;
	}
}

foreach (
	array(
		'start_date',
		'end_date',
		'returned_to_work_date',
	) as $n) {
		$data["Absence.{$n}"] = empty($data["Absence.{$n}"])?'':$time->format('d/m/y', $data["Absence.{$n}"]);
}

$json = array(
	'success'=>true,
	'data'=>$data
);

?>

<?=$javascript->object($json)?>
