<?php

class Status extends AppModel
{
	var $name         = 'Status';
	var $useTable     = 'sec_status';
	var $primaryKey   = 'status_code';

	var $displayField = 'status_description';
	
	const ACTIVE     = 'A';
	const NOT_ACTIVE = 'N';
	const OBSOLETE   = 'O';
	const SUSPENDED  = 'S';

	static $ALL_CODES = array(
		self::ACTIVE => array(
			'status_code'        => self::ACTIVE,
			'status_description' => 'Active',
		),
		self::NOT_ACTIVE => array(
			'status_code'        => self::NOT_ACTIVE,
			'status_description' => 'Not active',
		),
		self::OBSOLETE => array(
			'status_code'        => self::OBSOLETE,
			'status_description' => 'Obsolete',
		),
		self::SUSPENDED => array(
			'status_code'        => self::SUSPENDED,
			'status_description' => 'Suspended',
		),
	);

	function sync() {
		$this->saveAll(self::$ALL_CODES);
	}
}