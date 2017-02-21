<?php

//debug($person);
//return;

//$data = array();

if (!empty($person['Person']['date_of_birth'])) {
	$person['Person']['date_of_birth'] = $time->format('d/m/y', $person['Person']['date_of_birth']);
}

echo $javascript->object(
	array(
		'success' => true,
		'data'    => Set::flatten($person)
	)
);

return;

foreach (array('Person', 'Employee') as $model) {
	foreach ($person[$model] as $n=>$v) {
		$data["{$model}.{$n}"] = $v;
	}
}

$data['Person.full_name'] = $data['Person.first_name'] . ' ' . $data['Person.last_name'];
//$data['Employee.salary_number'] = intval($data['Employee.salary_number']);

$json = array(
	'success'=>true,
	'data'=>$data
);

?>

<?=$javascript->object($json, 
	array(
		'stringKeys' => array('success'),
		'quoteKeys'  => false
	)
)?>
