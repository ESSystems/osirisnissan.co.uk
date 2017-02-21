<?php
class AttendanceFeedbackController extends AppController
{
	var $name = 'AttendanceFeedback';
	var $uses = array('Appointment', 'Attendance', 'AttendanceFeedback', 'Referral', 'Referrer');

	function followers() {
		$success = false;
		$data = array();

		if (!empty($this->params['named']['attendance_id'])) {
			$attendance_id = $this->params['named']['attendance_id'];
			$referral_id = $this->Appointment->getReferralIdByAttendanceId($attendance_id);
			$referral = $this->Referral->find('first', array(
				'conditions' => array('Referral.id' => $referral_id),
				'contain' => array(
					'Referrer',
					'Referrer.Person',
					'Referrer.Organisation',
					'Referrer.ReferrerType',
					'Follower',
					'Follower.Person',
					'Follower.Organisation',
					'Follower.ReferrerType',
				)
			));

			if(!empty($referral)) {
				$data[] = $referral['Referrer'];
			}
			foreach ($referral['Follower'] as $key => $value) {
				$data[] = $value;
			}
			if(!empty($data)) {
				$success = true;
			}
		}

		return compact('success', 'data');
	}

	function load($attendance_id) {
		$this->set('attendance_feedback',
			$this->AttendanceFeedback->find('first',
				array(
					'conditions' => array(
						'attendance_id' => $attendance_id
					)
				)
			)
		);
	}

	function save() {
		//Configure::write('debug', 1);
		$errors = array();
		if (!empty($this->data)) {
			$existing_feedback = $this->AttendanceFeedback->find('first', array(
				'conditions' => array(
					'attendance_id' => $this->data['AttendanceFeedback']['attendance_id']
				)
			));
			$this->data['AttendanceFeedback']['id'] = $existing_feedback['AttendanceFeedback']['id'];
			$this->AttendanceFeedback->save($this->data);
		}

		$errors = Set::flatten(array('AttendanceFeedback' => $this->AttendanceFeedback->validationErrors));

		$this->set('status',
			array(
				'success' => empty($errors),
				'errors' => Set::flatten($errors)
			)
		);
	}

}
?>