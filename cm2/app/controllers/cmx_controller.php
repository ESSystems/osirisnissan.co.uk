<?php
class CmxController extends AppController
{
	var $uses = array('Referrer', 'Appointment', 'Diary');

	/**
	 * add beforeFilter to avoid authentication check
	 */
	function beforeFilter() {
		$this->layout = 'cmx';
	}

	function confirm_appointment($appointment_id, $user_id) {
		$referrer = $this->Referrer->read('email', $user_id);

		if(empty($referrer)) {
			echo "Your link expired or you're a mean person!";
			$this->redirect('/users/login.extjs');
		}

		$appointment = $this->Appointment->read(null, $appointment_id);

		$this->set('start_date', $appointment['Appointment']['from_date']);

		$this->Session->write('CMX.appointment_id', $appointment_id);
		$this->Session->write('CMX.diary_id', $appointment['Appointment']['diary_id']);
		$this->Session->write('CMX.referrer_id', $user_id);
	}

	function direct_diaries_index() {
		$diary_id = $this->Session->read('CMX.diary_id');

		$data = $this->Diary->index();

		foreach ($data as $k => $d) {
			if(in_array($d['Diary']['id'], array($diary_id, $diary_id + 100000))) {
				$data[$k]['Diary']['is_hidden'] = 0;
			}
		}

		return compact('success', 'data');
	}

	function direct_index() {
		$conditions = array();
		$diary_id = $this->Session->read('CMX.diary_id');
		$appointment_id = $this->Session->read('CMX.appointment_id');

		if (!empty($this->params['named']['start'])) {
			$conditions['start'] = $this->params['named']['start'];
		}
		if (!empty($this->params['named']['end'])) {
			$conditions['end'] = $this->params['named']['end'];
		}

		$conditions['diary_id'] = $diary_id;
		$conditions['not'] = array(
			'Appointment.state' => array(Appointment::STATE_DELETED, Appointment::STATE_REJECTED, Appointment::STATE_CLOSED)
		);

		$data = $this->Appointment->index($conditions);
		$success = ($data !== false);

		foreach ($data as $k => $d) {
			if($d['Appointment']['id'] != $appointment_id && !empty($d['Person'])) {
				$booked = array(
					'Appointment' => array(
						'title' => 'Already Booked',
						'from_date' => $d['Appointment']['from_date'],
            			'to_date' => $d['Appointment']['to_date'],
						'from_time' => $d['Appointment']['from_time'],
						'to_time' => $d['Appointment']['to_time'],
						'diary_id' => $d['Appointment']['diary_id'] + 100000
					),
					'Person' => array()
				);
				$data[$k] = $booked;
			}
		}

		return compact('success', 'data');
	}

	function direct_save() {
		if (!empty($this->data)) {
			if ($this->Appointment->saveAppointment($this->data)) {
				return array(
					'success' => true,
					'data' => array('Appointment' => 'Your new date for this appointment is saved'
				));
			}
		}

		return array(
			'success'=> false,
			'data' => array('Appointment' => $this->Appointment->validationErrors)
		);
	}
}
?>