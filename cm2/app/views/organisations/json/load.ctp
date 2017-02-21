<?php
$json = array();
foreach ($organisation as $model=>$fields) {
	foreach ($fields as $n=>$v) {
		$json["{$model}.{$n}"] = $v;
	}
}

echo $javascript->object(
	array(
		'success' => true,
		'data' => $json
	)
);

?>
