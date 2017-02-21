<?php

class Attendance extends AppModel
{
	var $name       = 'Attendance';
	var $useTable   = 'attendances';
	var $primaryKey = 'id';

	var $belongsTo = array(
		'Person' => array(
			'foreignKey' => 'person_id'
		),
		'AttendanceReason' => array(
			'foreignKey' => 'attendance_reason_code'
		),
		'AttendanceResult' => array(
			'foreignKey' => 'attendance_result_code'
		),
		'Clinic' => array(
			'foreignKey' => 'clinic_id'
		),
		'User' => array(
			'foreignKey' => 'clinic_staff_id'
		),
		'Diagnosis',
		'Employee' => array(
			'className' => 'Nemployee',
			'foreignKey' => 'employee_id'
		),
		'SeenBy' => array(
			'className'  => 'Person',
			'foreignKey' => 'clinic_staff_id'
		)
	);

	var $hasOne = array(
		'Followup',
		'RecallListItemEvent',
	    'Appointment'
	);

	var $validate = array(
		'person_id' => array(
			array(
				'rule' => 'notEmpty'
			)
		),
		'clinic_id' => array(
			BLANK_ERROR => VALID_NOT_EMPTY
		),
		'attendance_reason_code' => array(
			BLANK_ERROR => VALID_NOT_EMPTY
		),
		'attendance_result_code' => array(
			'rule' => 'validateNonpending'
		),
		'clinic_staff_id' => array(
			'rule' => 'validateNonpending',
		),
		'diagnosis_id' => array(
			'rule' => 'validateNonpending',
		),
	    
	    'outcome_id' => array(
	        'notempty' => array(
	            'rule' => 'validateOutcome',
	            'message' => 'Please specify the final outcome'
            )
        )
	);

	/**
	 * @var Nemployee
	 */
	var $Employee;

	function validateNonpending($data) {
		if (!$this->_pendingRecord) {
			foreach ($data as $v) {
				if (empty($v) || $v === 'null') {
					return false;
				}
			}
		}

		return true;
	}

	function beforeValidate() {
		$data = &$this->data['Attendance'];

//		if (!empty($data['seen_at_date_ext'])) {
//			$data['seen_at_date_ext'] = $this->toDate($data['seen_at_date_ext']);
//			$data['seen_at_time'] = date('Y-m-d H:i:s',
//				strtotime(@$data['seen_at_date_ext'] . ' ' . @$data['seen_at_time_ext'])
//			);
//		} else {
//			$data['seen_at_time'] = null;
//		}
//
//		if (!empty($data['attendance_date_ext'])) {
//			$data['attendance_date_ext'] = $this->toDate($data['attendance_date_ext']);
//			$data['attendance_date_time'] = date('Y-m-d H:i:s',
//				strtotime(@$data['attendance_date_ext'] . ' ' . @$data['attendance_time_ext'])
//			);
//		} else {
//			$data['attendance_date_time'] = null;
//		}

		if (empty($data['seen_at_time'])) {
			$data['seen_at_time'] = null;
		}

		$checkFields = array(
			'seen_at_time',
			'attendance_result_code',
			'clinic_staff_id',
			'diagnosis_id',
			'recall_event_id'
		);
		$this->_pendingRecord = true;
		foreach ($checkFields as $n) {
			if (!empty($data[$n]) && $data[$n] !== 'null') {
				$this->_pendingRecord = false;
				break;
			}
		}

		return true;
	}

	function afterFind($data, $primary = false) {
	    if ($data && isset($data[0]['Attendance'])) {
			foreach ($data as $i=>$r) {
				if (!isset($r['Attendance']) || empty($r['Attendance']['id'])) {
				    continue;
				}

				$a = &$data[$i]['Attendance'];
				if (!empty($a['seen_at_time'])) {
					$stamp = strtotime($a['seen_at_time']);
					$a['seen_at_date_ext'] = date('d/m/y', $stamp);
					$a['seen_at_time_ext'] = date('H:i', $stamp);
					if ($a['seen_at_time_ext'] == '00:00') {
						$a['seen_at_time_ext'] = '';
					}
				}
				if (!empty($a['attendance_date_time'])) {
					if ($a['attendance_date_time'] == '0000-00-00 00:00:00') {
						$a['attendance_date_time'] = '';
					} else {
						$stamp = strtotime($a['attendance_date_time']);
						$a['attendance_date_ext'] = date('d/m/y', $stamp);
						$a['attendance_time_ext'] = date('H:i', $stamp);
						if ($a['attendance_time_ext'] == '00:00') {
							$a['attendance_time_ext'] = '';
						}
					}
				}
				foreach (
					array(
						'seen_at_time_ext',
						'seen_at_date_ext',
						'attendance_date_ext',
						'attendance_time_ext') as $n) {
					if (empty ($a[$n])) {
						$a[$n] = '';
					}
				}
				$a['private'] = $this->isPrivateReferral($a['id']);
			}
		}

		return $data;
	}

	function beforeSave() {
		$d = &$this->data[$this->alias];

		if (empty($d['attendance_date_time'])) {
			$d['attendance_date_time'] = date('Y-m-d H:i:s');
		}
		if (!$this->_pendingRecord && empty($d['seen_at_time'])) {
			$d['seen_at_time'] = date('Y-m-d H:i:s');
		}

		unset($d['attendance_date_ext'],
			$d['attendance_time_ext'],
			$d['seen_at_date_ext'],
			$d['seen_at_time_ext']);

		if (empty($d['employee_id'])) {
			$d['employee_id'] = $this->Person->getCurrentEmployeeId($d['person_id']);
		}

		return true;
	}

	function afterSave() {
		$d = $this->data[$this->alias];
		if (!empty($d['seen_at_time']) && !empty($d['recall_event_id'])) {
			$this->RecallListItemEvent->onAttendance($d['recall_event_id'], $this);
		}
	}

	function getAssociatedReferralId($attendance_id) {
		$referral_query = "SELECT r.id FROM referrals AS r JOIN appointments as a ON a.referral_id = r.id  WHERE a.attendance_id = $attendance_id LIMIT 1";
		$referral_id = $this->query($referral_query);

		return $referral_id[0]['r']['id'];
	}

	function hide($id) {
		$this->id = $id;
		return $this->saveField('is_hidden', 1, array('validate'=>false, 'callbacks'=>false));
	}

	function isPrivateReferral($attendance_id) {
		$referral_query = "SELECT r.private FROM referrals AS r JOIN appointments as a ON a.referral_id = r.id  WHERE a.attendance_id = $attendance_id LIMIT 1";
		$referral_private = $this->query($referral_query);
		
		return !empty($referral_private[0]['r']['private']);
	}

	function makePrivateByReferralId($referral_id) {
		$query = "SELECT a.attendance_id FROM appointments AS a WHERE a.referral_id = $referral_id LIMIT 1";
		$attendance_ids = $this->query($query);

		foreach ($attendance_ids as $a) {
			$id = $a['a']['attendance_id'];
	        $this->id = $id;
	        if ($this->exists()) {
	            $this->save(
    				array(
    				    'id'    => $id,
    				    'no_work_contact' => 'Y'
				    )
	            );
	        }
	    }
	}

	function unhide($id) {
		$this->id = $id;
		return $this->saveField('is_hidden', 0, array('validate'=>false, 'callbacks'=>false));
	}
	
	function validateOutcome() {
        return empty($this->data[$this->alias]['is_discharged']) || !empty($this->data[$this->alias]['outcome_id']);
	}
}