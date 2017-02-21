<?php

class AttendanceReasonsController extends AppController
{
	var $name = 'AttendanceReasons';
	var $scaffold;
	
	/**
	 * @var AttendanceReason
	 */
	var $AttendanceReason;
	
	function index() {
		$attendanceReasons = $this->AttendanceReason->find('all', array('recursive'=>-1));

		$this->set(compact('attendanceReasons'));
	}
	 
	function direct_get_diary_reasons() {
		return array(
			'success' => true,
			'data' => $this->AttendanceReason->getDiaryReasons()
		);
	}
}