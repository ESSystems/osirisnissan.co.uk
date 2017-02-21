<?php

class User extends AppModel
{
	var $name       = 'User';
	var $useTable   = 'clinic_staff_member';

	var $belongsTo = array(
		'Person' => array(
			'foreignKey' => 'id'
		),
		'Status' => array (
			'foreignKey' => 'sec_status_code'
		)
	);

	var $hasAndBelongsToMany = array(
		'Group' => array(
		    'with' => 'UserGroup',
			'foreignKey' => 'user_id',
			'associationForeignKey' => 'group_id',
			'order' => 'group_name'
		),
		'Func' => array(
			'with' => 'UserFunction',
			'foreignKey' => 'user_id',
			'associationForeignKey' => 'function_id',
			'order' => 'function_name'
		),
	);
	
	var $validate = array(
		'sec_password' => array(
			array(
				'on' => 'create',
				'allowEmpty' => false,
				'required' => true,
				'rule' => VALID_NOT_EMPTY,
				'message' => 'Please enter password'
			)
		),
		'password_again' => array(
			array(
				'rule' => 'checkPassword',
				'message' => 'Passwords doesn\'t match.'
			)
		)
	);
	
	function checkPassword() {
		if (!empty($this->data['User']['sec_password']) && @$this->data['User']['password_again'] != $this->data['User']['sec_password']) {
			return false;
		}
		
		return true;
	}
}