<?php
echo $javascript->Object(
	array(
		'success' => true,
		'totalRows' => $paginator->counter('%count%'),
		'rows' => $data
	)
);
?>