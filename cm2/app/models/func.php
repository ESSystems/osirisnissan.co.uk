<?php

class Func extends AppModel
{
	var $name         = 'Func';
	var $useTable     = 'sec_function';
	var $displayField = 'function_name'; //scaffolding

	var $hasAndBelongsToMany = array(
		'Group' => array(
			'with' => 'GroupFunction',
			'foreignKey' => 'function_id',
			'associationForeignKey' => 'group_id'
		),
	);

	var $belongsTo = array(
		'Category' => array(
		),
		'Status' => array(
			'foreignKey' => 'status_code'
		)
	);

	var $validate = array(
		'function_name' => array (
			BLANK_ERROR => VALID_NOT_EMPTY
		),
		'category_id' => array (
			BLANK_ERROR => VALID_NOT_EMPTY
		)
	);

	function findAllByCategory() {
		$functions = $this->findAll(array('Func.status_code'=>'A'), null, 'Func.function_name ASC');
		$result = array();

		foreach($functions as $func) {
			$result[$func['Category']['category_name']][$func['Func']['id']] = $func['Func']['function_name'];
		}

		ksort($result);

		return $result;
	}
}