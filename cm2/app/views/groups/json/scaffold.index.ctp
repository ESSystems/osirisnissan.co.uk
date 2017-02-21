<?php
$rows = array();

foreach ($groups as $r) {
	$rows[] = array(
		'id' => $r['Group']['id'],
		'group_name' => $r['Group']['group_name'],
	);
}

$json = array(
	'success' => true,
	'rows'   => $rows
);
?>

<?=$javascript->object($json)?>