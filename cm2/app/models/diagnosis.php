<?php

class Diagnosis extends AppModel 
{
	var $name = 'Diagnosis';
	
	var $belongsTo = array(
		'ParentDiagnosis' => array(
			'className' => 'Diagnosis',
			'foreignKey' => 'parent_id'
		)
	);
}