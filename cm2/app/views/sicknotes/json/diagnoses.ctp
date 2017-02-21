<?php

echo $javascript->object(
	array(
		'success' => true,
		'totalRows' => count($diagnoses['Diagnosis']),
		'rows' => $diagnoses['Diagnosis']
	)
);

?>