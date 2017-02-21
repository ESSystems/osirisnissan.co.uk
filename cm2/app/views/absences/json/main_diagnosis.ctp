<?php

$rows = array();

foreach ($allowedDiagnosisCodes as $id=>$desc) {
	$rows[] = array('id'=>$id, 'description'=>$desc);
}

echo $javascript->object(
	array(
		'success' => true,
		'rows'    => $rows
	)
);

?>