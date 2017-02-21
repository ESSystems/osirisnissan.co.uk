<?php
$json = array_merge(
	array('success'=>true),
	compact('totalRows', 'rows')
);
?>

<?=$javascript->object($json)?>