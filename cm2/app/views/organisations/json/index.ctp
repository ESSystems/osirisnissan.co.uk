<?php
$rows = array(
//	array(
//		'id' => '',
//		'name' => '-'
//	)
);
foreach ($organisations as $r) {
	$rows[] = array(
		'id' => $r['Organisation']['OrganisationID'],
		'name' => $r['Organisation']['OrganisationName'],
	);
}

$json = array(
	'success' => true,
	'rows'   => $rows
);
?>

<?=$javascript->object($json)?>