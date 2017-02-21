<?php  echo $javascript->object(
	array(
		'success' => true,
		'totalRows' => $totalPeople,
		'rows' => $people
	)
) ?>