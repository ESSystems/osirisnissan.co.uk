<?php
echo $javascript->Object(
	array(
		'success' => true,
		'total'   => $paginator->counter('%count%'),
		'rows'    => $data
	)
);
?>