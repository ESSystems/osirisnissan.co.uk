<?php
$json = array();
foreach ($sicknote as $model=>$data) {
	if (!isset($data[0])) {
		foreach ($data as $n=>$v) {
			if ($n == 'start_date' || $n == 'end_date') {
				$v = $time->format('d/m/y', $v);
			}
			$json["{$model}.{$n}"] = $v;
		}
	}
}

echo $javascript->object(array('success'=>true, 'data'=>$json));
?>

