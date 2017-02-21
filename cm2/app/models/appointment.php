<?php
/**
 * @author stv
 *
 * @property Referral $Referral
 * @property User $User
 * @property Person $Person
 * @property Attendance $Attendance
 * @property Diary $Diary
 */
class Appointment extends AppModel
{
	var $name = 'Appointment';

	var $virtualFields = array(
		'from_time' => 'TIME(Appointment.from_date)',
		'to_time' => 'TIME(Appointment.to_date)'
	);

	var $actsAs = array(
		'Attribute' => array(
			'from_time',
			'to_time',
			'period',
			'passes_late_cancelation_condition'
		),
		'Notifier' => array(
			'status_fields' => array('from_date', 'to_date', 'state'),
			'target_model' => 'Referrer',
			'skip_values' => array(Appointment::STATE_BOOKED, Appointment::STATE_CONFIRMED)
		)
	);

	var $belongsTo = array(
		'User',
		'Person',
		'Attendance',
		'Diary',
		'Referral',
		'ReferrerType',
	    'Type' => array(
	        'className' => 'AttendanceReason',
	        'foreignKey' => 'type'
        ),
	    'DeleterUser' => array(
	        'className' => 'User',
	        'foreignKey' => 'deleted_by'
        ),
	    'Deleter' => array(
	        'className' => 'Person',
	        'foreignKey' => 'deleted_by'
        ),
	);

	var $validate = array(
		'diary_id' => array(
			'notempty' => array(
				'rule' => 'notempty'
			),
			'numeric' => array(
				'rule' => 'numeric'
			)
		),
		'person_id' => array(
			'notempty' => array(
				'rule' => 'notempty'
			),
			'numeric' => array(
				'rule' => 'numeric'
			)
		),
		'referrer_type_id' => array(
			'notempty' => array(
				'rule' => 'notempty'
			),
			'numeric' => array(
				'rule' => 'numeric'
			)
		),
		'referrer_name' => array(
			'notempty' => array(
				'rule' => 'notempty'
			)
		),
		'from_date' => array(
			'notempty' => array(
				'rule' => 'notempty'
			),
			'availability' => array(
				'rule' => 'validateAvailability',
				'message' => 'The date you selected is not available. Please choose another date.'
			)
		),
		'to_date' => array(
			'notempty' => array(
				'rule' => 'notempty'
			),
		),
		'length' => array(
			array(
				'rule' => 'validateLength'
			)
		)
	);

	var $referral_id = 0;

	/**
	 * Appointment states
	 */
    const STATE_NEW       = 'new';
    const STATE_CONFIRMED = 'confirmed';
    const STATE_BOOKED    = 'booked';
    const STATE_CLOSED    = 'closed';
    const STATE_REJECTED  = 'rejected';
    const STATE_DELETED   = 'deleted';

	function beforeValidate() {
		$d = &$this->data[$this->alias];

		if (!empty($d['from_date'])) {
			$d['from_date'] = date('Y-m-d H:i:s', strtotime($d['from_date']));
		}
		if (!empty($d['to_date'])) {
			$d['to_date'] = date('Y-m-d H:i:s', strtotime($d['to_date']));
		}

		if (!empty($d['from_date']) && !empty($d['from_time'])) {
			$d['from_date'] .= ' ' . $d['from_time'];
			unset($d['from_time']);
		}

		if (empty($d['to_date']) && !empty($d['length']) && is_numeric($d['length'])) {
			$from = strtotime($d['from_date']);
			$to   = $from + $d['length'] * 60;
			$d['to_date'] = date('Y-m-d H:i:s', $to);
		}

		return true;
	}

	function beforeSave() {
		$d = &$this->data[$this->alias];

		if (!$this->exists()) {
			$this->data[$this->alias]['user_id'] = CurrentUser::id();
		}

		if (empty($d['type'])) {
			$d['type'] = $this->Diary->field('default_appointment_type', array('Diary.id'=>$d['diary_id']));
		}

		return true;
	}

	function afterSave($created) {
		if ($created) {
			$this->Referral->setState('accepted', $this->data[$this->alias]['referral_id']);
		}
	}

	function afterFind($data, $primary = false) {
		if (!$primary) {
			$data = $this->Behaviors->Attribute->afterFind($this, $data, true);
		}

		return $data;
	}

	function from_time($row) {
		if (!empty($row[$this->alias]['from_date'])) {
			return $this->appointmentDate($row[$this->alias]['from_date']);
		}
	}

	function to_time($row) {
		if (!empty($row[$this->alias]['to_date'])) {
			return $this->appointmentDate($row[$this->alias]['to_date']);
		}
	}

	function period($row) {
		if (!empty($row[$this->alias]['to_date'])) {
			return
				date('d.m.Y', strtotime($row[$this->alias]['to_date'])) . ', ' .
				$this->from_time($row) . ' - ' . $this->to_time($row);
		}
	}

	function validateLength() {
		$d = &$this->data[$this->alias];

		if (!empty($d['from_date']) && !empty($d['to_date'])) {
			return true;
		}

		return is_numeric($d['length']);
	}

	function validateAvailability() {
		$d = &$this->data[$this->alias];

		if (empty($d['from_date']) || empty($d['to_date']) || empty($d['diary_id'])) {
			return true;
		}

		if (!empty($d['state']) && in_array($d['state'], array(static::STATE_DELETED, static::STATE_REJECTED))) {
		    return true;
		}

		if(strtotime($d['from_date']) > strtotime($d['to_date'])) {
			return false;
		}

		$conditions = array(
			'diary_id' => $d['diary_id'],
			'NOT' => array(
				'state' => array(self::STATE_CLOSED, self::STATE_REJECTED, self::STATE_DELETED)
			),
			'to_date >' => $d['from_date'],
			'from_date <' => $d['to_date'],
		);

		if (!empty($d[$this->primaryKey])) {
			$conditions[$this->primaryKey . ' != '] = $d[$this->primaryKey];
		}

		if ($this->find('count',
			array(
				'contain' => array(),
				'conditions' => $conditions
			)
		) != 0) {
			return FALSE;
		}

		if ($this->Diary->DiaryRestriction->isRestricted($d['from_date'], $d['to_date'], $d['diary_id'])) {
			return FALSE;
		}

		// At this point appointment data is validated, which means that this appointment is not
		// in collision with another appointmer or NPT rule.
		$d['in_collision'] = false;

		return TRUE;

	}

	function makeAttendance($ids, $extraData = array())
	{
		if (!is_array($ids)) {
			$ids = array($ids);
		}

		$success = true;

		foreach ($ids as $id) {
			$apptData = $this->find('first',
				array(
					'contain' => array('Diary(default_appointment_type)'),
					'conditions' => array(
						'Appointment.id' => $id,
						//'Appointment.state' => 'confirmed'
					)
				)
			);

			if (!$apptData) {
				continue;
			}

			$reason = $apptData['Appointment']['referral_reason_id'];
			if (empty($reason)) {
			    $reason = $apptData['Diary']['default_appointment_type'];
			}

			$attnData = array(
				'person_id' => $apptData['Appointment']['person_id'],
				'attendance_reason_code' => $reason,
				'attendance_date_time' => $apptData['Appointment']['from_date'],
				'clinic_id' => 1,
				'no_work_contact' => $this->isPrivateReferral($apptData['Appointment']['referral_id'])
			);

			// Complete Attendance fields to create a 'Late Cancellation'
			if(!empty($extraData)) {
				$attnData = array_merge($attnData, $extraData);
			}

			$this->Attendance->create($attnData);
			if ($this->Attendance->save()) {
				$this->id = $id;
				$this->save(
    				array(
    				    'attendance_id' => $this->Attendance->id,
    				    'state' => Appointment::STATE_BOOKED
				    )
	            );
				// $this->saveField('attendance_id', $this->Attendance->id);
				// $this->setState('booked', $id);
			} else {
				$success = false;
			}
		}

		return $success;
	}

	function setState($state, $ids) {
		if (!empty($ids)) {
			return $this->updateAll(
				array('Appointment.state' => "'{$state}'"),
				array('Appointment.id' => $ids)
			);
		}

		return false;
	}

	function getReference() {
		if($this->referral_id == 0) {
			$this->referral_id = $this->getReferralId($this->id);
		}

		$query = "SELECT Referral.case_reference_number FROM referrals AS Referral JOIN appointments AS Appointment "
				. "ON Referral.id = Appointment.referral_id "
				. "WHERE Referral.id=$this->referral_id LIMIT 1";
		$referral = $this->query($query);

		return "Case reference number: " . $referral[0]['Referral']['case_reference_number'];
	}

	function getReferralId($a_id) {
		$query = "SELECT a.referral_id FROM appointments AS a "
				. "WHERE a.id=$a_id LIMIT 1";
		$referral_id = $this->query($query);

		if($referral_id == '') {
			$this->log("No Referral was found for the Appointment with id: " . $a_id);
		}

		return $referral_id[0]['a']['referral_id'];
	}

	function getReferralIdByAttendanceId($attendance_id) {
		$query = "SELECT a.referral_id FROM appointments AS a "
				. "WHERE a.attendance_id=$attendance_id LIMIT 1";
		$referral_id = $this->query($query);

		if($referral_id == '') {
			$this->log("No Referral was found in Appointment for the Attendance with id: " . $attendance_id);
		}

		return $referral_id[0]['a']['referral_id'];
	}

	function isPrivateReferral($referral_id) {
		$referral = ClassRegistry::init('Referral');
		return $referral->isPrivate($referral_id) == 1 ? 'Y' : 'N';
	}

	function notifierLink() {
		if($this->referral_id == 0) {
			$this->referral_id = $this->getReferralId($this->id);
		}

		return Configure::read('CMX.view_urls.Referral') . $this->referral_id;
	}

	/**
	 * Used by Notification model to display the date
	 * @param datetime $date
	 */
	function from_date($date) {
		return $this->appointmentDate($date);
	}

	/**
	 * Used by Notification model to display the date
	 * @param datetime $date
	 */
	function to_date($date) {
		return $this->appointmentDate($date);
	}

	function appointmentDate($date) {
		if ($date != '') {
			return date('H:i', strtotime($date));
		}
	}

	function collisions($diaryId)
	{
		$conditions = array(
			'Appointment.diary_id' => $diaryId,
			'Appointment.in_collision' => true
		);

		return $this->find('all', $this->prepareFindQuery($conditions));
	}

	/**
	 * Returns the appointments with specified conditions.
	 *
	 * valid $conditions are:
	 * 		start
	 * 		end
	 * 		state
	 * 		diary_id
	 *
	 * @param array $conditions
	 */
	function index($conditions, $bNpt = false) {
		$cond = $this->prepareFindConditions($conditions);

		$data = $this->find('all', $this->prepareFindQuery($cond));

		if (!$data) {
			$data = array();
		}

		if ($bNpt || !isset($conditions['state'])) {
    		// Generate non-patient time data
    		$diary_restriction_model = ClassRegistry::init('DiaryRestriction');
    		$pappt = $diary_restriction_model->getPseudoAppointments($conditions['start'], $conditions['end'], $conditions['diary_id']);

    		foreach ($pappt as $i=>$v) {
    			$pappt[$i]['Person'] = array();
    		}

    		$data = am($data, $pappt);
		}

		return $data;
	}

	/**
	 * Translate custom conditions array to array of conditions suitable for find() and paginate.
	 *
	 * @param array $conditions @see Appointment::index()
	 * @return array
	 */
	public function prepareFindConditions($conditions)
	{
	    $cond = array();

	    if(isset($conditions['start'])) {
	        $cond['DATE(Appointment.from_date) >='] = $conditions['start'];
	    }
	    if(isset($conditions['end'])) {
	        $cond['DATE(Appointment.to_date) <='] = $conditions['end'];
	    }
	    if(isset($conditions['state'])) {
	        if($conditions['state'] == 'scheduled') {
	            $cond['Appointment.attendance_id'] = null;
	        } else {
	            $cond['Appointment.state'] = $conditions['state'];
	        }
	    }
	    if(isset($conditions['diary_id'])) {
	        $cond['Appointment.diary_id'] = $conditions['diary_id'];
	    }
	    if(isset($conditions['not'])) {
	    	$cond['NOT'][] = $conditions['not'];
	        $cond['Appointment.diary_id'] = $conditions['diary_id'];
	    }

	    return $cond;
	}


	public function prepareFindQuery($conditions)
	{
	    return array(
	        'contain' => array(
	            'Diary',
	            'Person',
	            'Referral',
	            'Referral.Referrer',
	            'Referral.Referrer.Organisation',
	            'Referral.Referrer.Person',
	            'Referral.Referrer.ReferrerType',
	            'Referral.PatientStatus',
	            'Referral.ReferralReason',
	            'Referral.OperationalPriority',
	            'ReferrerType'
	        ),
	        'conditions' => $conditions,
	        'order' => array('Appointment.from_date'),
	    );
	}


	public function buildFindQuery($conditions)
	{
	    $conditions = $this->prepareFindConditions($conditions);
	    $query      = $this->prepareFindQuery($conditions);

	    return $query;
	}

	function isNewOrReview($data) {
		if(isset($data['Appointment']['id'], $data['Appointment']['diary_id'], $data['Appointment']['person_id'], $data['Appointment']['referral_id'])) {
			$conditions = array(
				'Appointment.diary_id' => $data['Appointment']['diary_id'],
				'Appointment.person_id' => $data['Appointment']['person_id'],
				'Appointment.referral_id' => $data['Appointment']['referral_id']
			);
			if($data['Appointment']['id'] != '') {
				$conditions['Appointment.id ='] = $data['Appointment']['id'];
				$a = $this->find('first', array(
					'contain' => array(),
					'conditions' => $conditions,
					'fields' => 'Appointment.new_or_review'
				));
				if(isset($a['Appointment']['new_or_review'])) {
					return $a['Appointment']['new_or_review'];
				} else {
					return 'review';
				}
			} else {
				$a = $this->find('count', array(
					'contain' => array(),
					'conditions' => $conditions
				));
				if($a > 0) {
					return 'review';
				}
			}
		}
		return 'new';
	}

	/**
	 * Set `is_collision` flag to TRUE or FALSE for all active appointments
	 *
	 * @param int $diaryId
	 */
	function markCollisions($diaryId)
	{
		$data = $this->find('all',
			array(
				'contain' => array(),
				'conditions' => array(
					'Appointment.diary_id' => $diaryId,
					'Appointment.state' => array('new', 'confirmed', 'booked')
				),
				'fields' => array('id', 'from_date', 'to_date', 'diary_id', 'in_collision')
			)
		);

		$result = array(
			'conflict' => 0,
			'marked' => 0,
			'cleaned' => 0,
		);

		foreach ($data as $r) {
			$d = $r['Appointment'];
			$inCollision = $this->Diary->DiaryRestriction->isRestricted($d['from_date'], $d['to_date'], $d['diary_id']);

			if ($d['in_collision'] != $inCollision) {
				$this->id = $d['id'];
				$this->saveField('in_collision', $inCollision, false);

				$result[$inCollision ? 'marked' : 'cleaned']++;
			}

			if ($inCollision) {
				$result['conflict']++;
			}
		}

		return $result;
	}

	function saveAppointment($app_data) {
		$data = array();

		foreach($app_data as $f=>$v) {
			$parts = explode('.', $f);
			$val   = &$data;
			foreach ($parts as $p) {
				if (!isset($val[$p])) {
					$val[$p] = null;
				}
				$val = &$val[$p];
			}

			$val = $v;
		}

		$app_data = $data;

		// find existing appointment and load existing information for a followup referal
		if(isset($app_data['Attendance']['id'])) {
			$existing_appointment = $this->Appointment->find('first', array(
				'conditions' => array (
					'attendance_id' => $app_data['Attendance']['id']
				)
			));

			$app_data['Appointment']['referral_id'] = $existing_appointment['Appointment']['referral_id'];
			$app_data['Appointment']['person_id'] = $existing_appointment['Appointment']['person_id'];
			$app_data['Appointment']['case_nature'] = $existing_appointment['Appointment']['case_nature'];
			$app_data['Appointment']['case_reference_number'] = $existing_appointment['Appointment']['case_reference_number'];
			$app_data['Appointment']['referral_reason_id'] = $existing_appointment['Appointment']['referral_reason_id'];
			$app_data['Appointment']['state'] = 'confirmed';
		}

		if($this->save($app_data)) {
			return true;
		}

		return false;
	}

	function targetIds() {
		if($this->referral_id == 0) {
			$this->referral_id = $this->getReferralId($this->id);
		}

		$referral = ClassRegistry::init('Referral');

		return $referral->getFollowers($this->referral_id);
	}

	/**
	 * Soft-deleting an appointment record
	 *
	 * @params int $id
	 * @return boolean
	 */
	function delete($id, $reason) {
	    $data = $this->find('first',
	        array(
	            'contain' => array(),
	            'conditions' => array(
	                $this->primaryKey => $id,
	                'NOT' => array(
	                    'state' => array(static::STATE_DELETED, static::STATE_REJECTED)
                    )
                ),
	            'fields' => array($this->primaryKey, 'referral_id')
            )
	    );

	    if (!$data) {
	        return false;
	    }

	    $data = am($data[$this->alias],
	        array(
	            'state'          => self::STATE_DELETED,
	            'deleted_by'     => CurrentUser::id(),
	            'deleted_on'     => date('Y-m-d H:i:s'),
	            'deleted_reason' => $reason,
            )
        );

	    /*
	     * No need to alter the referral after delete
	     *
	     * @link http://projects.tripledub.net/projects/54/comments/7700
	     */
	    /*
	    if (!empty($data['referral_id']) && $this->getFirstAppointmentId($data['referral_id']) == $id) {
	        // This appointment is the first appointment of its referral
	        $this->Referral->setState(Referral::STATE_NEW, $data['referral_id']);
	    }
	    */

	    return $this->save($data);
	}



	/**
	 * Return the oldest of all appointments associated with given referral
	 *
	 * @param int $referralId
	 * @return int
	 */
	function getFirstAppointmentId($referralId){
	    $data = $this->find('first',
    	    array(
    	        'contain' => array(),
    	        'conditions' => array(
    	            'referral_id' => $referralId,
	                'NOT' => array(
	                    'state' => array(static::STATE_DELETED, static::STATE_REJECTED)
                    ),
    	        ),
    	        'order' => 'created ASC',
    	        'fields' => $this->primaryKey,
    	    )
	    );

	    if (!$data) {
	        return false;
	    }

	    return $data[$this->alias][$this->primaryKey];
	}


	/**
	 *
	 * @param array $scope array(
	 *     'after'      => datetime(NULL = now),
	 *     'diary_id'   => int(NULL = all diaries of a specified type),
	 *     'diary_type' => key(attendance_reasons)(NULL = any type),
	 *     'length      => int(NOT NULL) - required min. length of the time slot
     * )
	 */
	function findNextGap($scope) {
	    $scope['after']  = !empty($scope['after']) ? date('Y-m-d H:i:s', strtotime($scope['after'])) : date('Y-m-d H:i:s');
	    $scope['length'] = !empty($scope['length']) ? $scope['length'] : 10;

	    $conditions = array();

	    if (!empty($scope['diary_id'])) {
    	    $conditions['Appointment.diary_id'] = $scope['diary_id'];
	    }

// 	    if (!empty($scope['diary_type'])) {
//     	    $conditions['Diary.default_appointment_type'] = $scope['diary_type'];
// 	    }

	    $data = $this->find('first',
	        array(
	            'contain' => array('Diary(id,name,default_appointment_type)'),
	            'conditions' => $conditions,
	            'fields' => array(
	                'Appointment.id',
	                'Appointment.from_date',
	                'Appointment.to_date',
	                '(SELECT MIN(from_date) FROM appointments WHERE diary_id = Appointment.diary_id AND from_date >= Appointment.to_date) AS `next_date`',
	                "GREATEST('{$scope['after']}', Appointment.to_date) AS `avail_from`",
	                "DATE_ADD(GREATEST('{$scope['after']}', Appointment.to_date), INTERVAL {$scope['length']} MINUTE) AS `avail_to`",
                ),
                'order' => 'Appointment.from_date',
	            'group' => "Appointment.id HAVING DATE_SUB(`next_date`, INTERVAL {$scope['length']} MINUTE) >= `avail_from` OR `next_date` IS NULL"
            )
	    );

	    if (empty($data[0]['next_date'])) {
	        $data[0]['next_date'] = date('Y-m-d', strtotime($data[0]['avail_to'])) . ' 23:59:59';
	    }

	    return $data;
	}

	function passes_late_cancelation_condition($row) {
		if (!empty($row[$this->alias]['from_date'])) {
			$from_date = date('Y-m-d H:i:s');
			$to_date = date('Y-m-d H:i:s', time() + 60*60*Configure::read('Late_cancelation_condition'));
			if ($row[$this->alias]['from_date'] <= $to_date && $from_date <= $row[$this->alias]['to_date']) {
				return false;
			}

			return true;
		}
	}
}
?>