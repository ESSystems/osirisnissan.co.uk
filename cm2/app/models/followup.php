<?php
class Followup extends AppModel 
{
	var $name = 'Followup';

	var $belongsTo = array(
		'ResultAttendance' => array(
			'className' => 'Attendance',
			'foreignKey' => 'result_attendance_id',
		),
		'Attendance' => array(
			'className' => 'Attendance',
			'foreignKey' => 'attendance_id',
		),
		'Person',
	);
	
	var $validate = array(
		'date' => array(
			'rule' => 'checkDate'
		)
	);
	
	function beforeValidate() {
		if (!empty($this->data['Followup']['date'])) {
			$this->data['Followup']['date'] = $this->toDate($this->data['Followup']['date'], 'future');
		}

		return true;
	}
	
	function checkDate() {
		return $this->data[$this->alias]['type'] == 'no' || $this->data[$this->alias]['type'] == 'null' || empty($this->data[$this->alias]['type']) || !empty($this->data[$this->alias]['date']);
	}
}
?>