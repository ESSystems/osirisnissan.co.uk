<?php

$json = array(
	'success' => true,
	'totalRows' => count($groups),
	'id' => 'id',
	'rows' => $groups
);

?>

<?=$javascript->object($json)?>