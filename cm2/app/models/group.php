<?php

class Group extends AppModel
{
	var $name         = 'Group';
	var $useTable     = 'sec_group';
	var $displayField = 'group_name'; // Scaffolding

	var $hasAndBelongsToMany = array(
		'Func' => array(
			'with' => 'GroupFunction',
			'foreignKey' => 'group_id',
			'associationForeignKey' => 'function_id',
			'order' => 'Func.function_name ASC'
		),
	);

	var $belongsTo = array(
		'Status' => array(
			'foreignKey' => 'status_code'
		)
	);

	var $validate = array(
		'group_name' => array(
			BLANK_ERROR => VALID_NOT_EMPTY
		)
	);
	
	const NURSES            = 1;
	const PHYSIO            = 2;
	const SYSTEM_SUPERVISOR = 3;
	const ADMIN             = 4;
	const SITE_SUPERVISOR   = 5;
	const EXPORT            = 6;
	const TRIAGE            = 7;
	
	const STATUS_ACTIVE = 'A';
	
	static $ALL_GROUPS = array(
			self::NURSES => array(
				'id'          => self::NURSES,
				'group_name'  => 'Nurses',
				'status_code' => self::STATUS_ACTIVE
			),
			self::PHYSIO => array(
				'id'          => self::PHYSIO,
				'group_name'  => 'Physio',
				'status_code' => self::STATUS_ACTIVE
			),
			self::SYSTEM_SUPERVISOR => array(
				'id'          => self::SYSTEM_SUPERVISOR,
				'group_name'  => 'System supervisor',
				'status_code' => self::STATUS_ACTIVE
			),
			self::ADMIN => array(
				'id'          => self::ADMIN,
				'group_name'  => 'Admin',
				'status_code' => self::STATUS_ACTIVE
			),
			self::SITE_SUPERVISOR => array(
				'id'          => self::SITE_SUPERVISOR,
				'group_name'  => 'Site supervisor',
				'status_code' => self::STATUS_ACTIVE
			),
			self::EXPORT => array(
				'id'          => self::EXPORT,
				'group_name'  => 'Export',
				'status_code' => self::STATUS_ACTIVE
			),
			self::TRIAGE => array(
				'id'          => self::TRIAGE,
				'group_name'  => 'Triage',
				'status_code' => self::STATUS_ACTIVE
			),
	);

	function sync() {
		$this->saveAll(self::$ALL_GROUPS);
	}
}