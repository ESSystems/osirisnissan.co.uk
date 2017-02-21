<?php
$rows = array(
	array(
		'id' => '',
		'name' => '-',
	)
);
foreach ($users as $r) {
	$suffix = '';
	if ($r['User']['sec_status_code'] != 'A') {
		$suffix = ' (' . $r['User']['sec_status_code'] . ')';
	}
	$rows[] = array(
		'id'   => $r['User']['id'],
		'name' => $r['Person']['first_name'] . ' ' . $r['Person']['last_name'] . $suffix
	);
}

$json = array(
	'success' => true,
	'rows'   => $rows
);
?>

<?=$javascript->object($json)?>