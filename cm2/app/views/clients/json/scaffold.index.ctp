<?php
$rows = array(
	array(
		'id' => '',
		'ClientName' => '-'
	)
);
foreach ($clients as $r) {
	$rows[] = array(
		'id' => $r['Client']['ClientID'],
		'ClientName' => $r['Client']['ClientName'],
	);
}

$json = array(
	'success' => true,
	'rows'   => $rows
);
?>

<?=$javascript->object($json)?>