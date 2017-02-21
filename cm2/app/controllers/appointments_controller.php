<?php
class AppointmentsController extends AppController
{
	var $name = 'Appointments';
	
	var $uses = array('Appointment', 'DiaryRestriction');

	/**
	 * @var Appointment
	 */
	var $Appointment;
	
	/**
	 * @var DiaryRestriction
	 */
	var $DiaryRestriction;
	
	function direct_index($filter = null) {
		$conditions = array();
		if (isset($filter)) {
			$conditions = $filter;
		} else {
			if (!empty($this->params['named']['from'])) {
				$conditions['start'] = $this->params['named']['from'];
			}
			if (!empty($this->params['named']['to'])) {
				$conditions['end'] = $this->params['named']['to'];
			}
		}
		
		if (isset($this->params['named']['Appointment.state'])) {
			$conditions['state'] = $this->params['named']['Appointment.state'];
		}
		if (isset($this->params['named']['diary_id'])) {
			$conditions['diary_id'] = $this->params['named']['diary_id'];
		}
		
		if (!empty($conditions['state'])) {
    		$this->paginate['Appointment'] = am(
    		    $this->paginate,
    		    $this->Appointment->buildFindQuery($conditions),
    		    $this->initPaging()
    		);
    		
    		$this->paginate['Appointment']['conditions']['NOT'][] = array(
    		    'Appointment.state' => array(Appointment::STATE_DELETED, Appointment::STATE_REJECTED, Appointment::STATE_CLOSED)
		    );
    		
    		// {{{ Hack
    		if (isset($this->paginate['Appointment']['order']) && preg_match('/^Appointment\.title\s+(asc|desc)$/i', $this->paginate['Appointment']['order'], $m)) {
    		    unset($this->params['named']['sort']);
    		    unset($this->passedArgs['sort']);
    		    $this->passedArgs['order'] = array('Person.last_name' => $m[1], 'Person.first_name'=>$m[1],);
    		}
    		// }}} Hack
    		
    		$data = $this->paginate($this->Appointment);
    		$total = $this->params['paging']['Appointment']['count'];
		} else {
		    $conditions['state'] = array(
		        Appointment::STATE_BOOKED, Appointment::STATE_CONFIRMED, Appointment::STATE_NEW,
	        );
		    $data = $this->Appointment->index($conditions, TRUE);
		    $total = count($data);
		}
		
		$success = ($data !== false);
		
		return compact('success', 'data', 'total');
	}
	
	function daily($diaryId, $date) {
		$date = new Date($date);
		
		$conditions = array(
			'diary_id' => $diaryId,
			'start'    => $date->dayStart()->string('Y-m-d H:i:s'),
			'end'      => $date->dayEnd()->string('Y-m-d H:i:s'),
		);

		$data = $this->Appointment->index($conditions);
		
		usort($data, array($this, 'dailySort'));
		
		$diaryData = $this->Appointment->Diary->find('first',
			array(
				'contain' => array(),
				'conditions' => array(
					'Diary.id' => $diaryId
				)
			)
		);
		
		if ($success = ($data !== false)) {
			$peopleIds = Set::extract($data, '/Appointment/person_id');
			$employeeData = $this->Appointment->Person->findEmployeeData($peopleIds, 
				array(
					'contain' => array('Supervisor', 'Department')
				)
			);
			$employeeData = Set::combine($employeeData, '/Employee/person_id', '/');
		};

		$this->set(compact('success', 'data', 'diaryData', 'employeeData'));
		$this->set('date', $date->string('Y-m-d'));
	}
	
	private static function dailySort($l, $r) {
		if ($l['Appointment']['from_time'] == $r['Appointment']['from_time']) {
			return 0;
		} elseif ($l['Appointment']['from_time'] < $r['Appointment']['from_time']) {
			return -1;
		}
		
		return 1;
	}
	
	function direct_save() {
		if (!empty($this->data)) {

			if ($this->Appointment->saveAppointment($this->data)) {
				if (empty($this->data['Appointment']['id']) || $this->data['Appointment']['id'] != $this->Appointment->id) {
					$this->Session->delete('Accepted');
				}
				return array(
					'success' => true,
					'data' => $this->Appointment->find('first',
						array(
							'contain' => array('Person'),
							'conditions' => array('Appointment.id' => $this->Appointment->id),
						)
					),
				);
			}
		}

		return array(
			'success'=> false, 
			'data' => array('Appointment' => $this->Appointment->validationErrors)
		);
	}
	
	function direct_delete() {
		if ($this->Appointment->delete($this->params['named']['id'], $this->params['named']['reason'])) {
			return array('success'=>true);
		}
		
		return array('success'=>false);
	}

	function direct_make_attendance() {
		$success = false;
		$errors  = array();
		if (!empty($this->params['pass'])) {
			$success = $this->Appointment->makeAttendance($this->params['pass']);
			if (!$success) {
			    $errors = Set::flatten(
			        array(
			            'Appointment' => $this->Appointment->validationErrors,
			            'Attendance' => $this->Appointment->Attendance->validationErrors,
		            )
			    );
			}
		}
		
		return compact('success', 'errors');
	}
	
	function direct_confirmed() {
		$success = false;
		if (!empty($this->params['pass'])) {
			$success = $this->Appointment->setState(Appointment::STATE_CONFIRMED, $this->params['pass']);
		}
		
		return compact('success');
	}
	
	function direct_reject() {
		$success = false;
		if (!empty($this->params['pass'])) {
			$success = $this->Appointment->setState(Appointment::STATE_REJECTED, $this->params['pass']);
		}
		
		return compact('success');
	}
	
	function direct_closed() {
		$success = false;
		if (!empty($this->params['pass'])) {
			$success = $this->Appointment->setState(Appointment::STATE_CLOSED, $this->params['pass']);
		}
		
		return compact('success');
	}
	
	function direct_conflicts() {
		if (empty($this->params['named']['diary_id'])) {
			return array('success' => false);
		}
		
		$data = $this->Appointment->collisions($this->params['named']['diary_id']);
		$success = ($data !== false);

		return compact('success', 'data');
	}
	
	function get_appointment_by_attendance_id($attendance_id) {
		$conditions = array(
			'Appointment.attendance_id' => $attendance_id
		);
		
		$data = $this->Appointment->find('first',
			array(
			    'contain'    => array('Referral'),
				'conditions' => $conditions
			)
		);
		
		$this->set('status',
			array(
				'success' => true,
				'data' => $data
			)
		);
	}
	
	/**
	 * Set `is_collision` flag to TRUE or FALSE for all active appointments
	 *  
	 * @param int $diaryId
	 */
	function direct_mark_collisions($diaryId) {
		$result = $this->Appointment->markCollisions($diaryId);
		
		return array(
			'success' => !!$result,
			'result'  => $result
		);
	}
	
	function direct_is_new_or_review() {
		$result = $this->Appointment->isNewOrReview($this->data);
		
		return array(
			'success' => !!$result,
			'result'  => $result
		);
	}

	function summary($personId)
	{
	    $data = $this->Appointment->find('all',
    	    array(
    	        'contain' => array(
    	            'Type',
    	            'Diary(name)',
    	            'Attendance',
    	            'Referral.Referrer.Person(first_name, last_name)',
    	            'Deleter(first_name, last_name)',
    	        ),
    	        'conditions' => array(
    	            'Appointment.person_id' => $personId
    	        ),
    	        'order' => 'Appointment.from_date'
    	    )
	    );
	     
	    if (!empty($this->params['requested'])) {
	        return $data;
	    }
	     
	}
}
?>