<?php
$rows = array(
	array(
		'status_code' => '',
		'status_description' => '-'
	)
);
foreach ($statuses as $r) {
	$rows[] = array(
		'status_code' => $r['Status']['status_code'],
		'status_description' => $r['Status']['status_description'],
	);
}

$json = array(
	'success' => true,
	'rows'   => $rows
);
?>

<?=$javascript->object($json)?>