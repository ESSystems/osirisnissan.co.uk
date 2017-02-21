<?php

class AttendanceReason extends AppModel
{
	var $name     = 'AttendanceReason';
	var $useTable = 'attendance_reasons';
	var $primaryKey = 'code';
	var $displayField = 'description';
	
	static public $diaryReasons = array(
		array(
			'code'=>'x', 
			'description' => 'Diary Appointment OH Physician'
		),
		array(
			'code'=>'ISOC', 
			'description' => 'Diary Appointment Isocom'
		),
		array(
			'code'=>'COUN', 
			'description' => 'Diary Appointment Counsellor'
		),
		array(
			'code'=>'PHYS', 
			'description' => 'Diary Appointment Physiotherapy'
		),
		array(
			'code'=>'NURS', 
			'description' => 'Diary Appointment Nurse'
		),
	);
	
	function getDiaryReasons() {
		return $this->find('all', array(
			'conditions' => array('diary_reason' => true)
		));
	}
}