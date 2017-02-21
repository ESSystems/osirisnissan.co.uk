<?php

class Patient extends AppModel 
{
	var $name = 'Patient';
	var $primaryKey = 'PersonID';
	
	var $belongsTo = array(
		'Person' => array(
			'foreignKey' => 'PersonID',
		),
		'Organisation' => array (
			'foreignKey' => 'ResponsibleOrganisationID'
		)
	);
	
	var $validate = array(
		'ResponsibleOrganisationID' => array(
			'notEmpty' => array(
				'rule' => 'notEmpty',
				'required' => true,
				'allowEmpty' => false
			)
		)
	);
}