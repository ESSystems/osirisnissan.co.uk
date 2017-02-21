<?php 
$json = array(
	'success' => true,
	'totalRows' => count($sicknotes),
	'rows' => $sicknotes
);

echo $javascript->object($json, 
	array(
		'stringKeys' => array('success'),
		'quoteKeys'  => false
	)
);
?>