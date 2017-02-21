<?php

$data = array();

foreach ($referrer as $model=>$d) {
	if (!isset($d[0])) {
		foreach ($d as $n=>$v) {
			$data["{$model}.{$n}"] = $v;
		}
	} else {
		$data["{$model}.{$n}"] = $d;
	}
}

$json = array(
	'success'=>true,
	'data'=>$data
);

?>

<?=$javascript->object($json)?>
